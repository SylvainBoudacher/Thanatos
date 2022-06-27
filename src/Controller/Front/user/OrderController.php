<?php

namespace App\Controller\Front\user;

use App\Repository\AddressOrderRepository;
use App\Utils\Functions;
use App\Entity\Address;
use App\Entity\AddressOrder;
use App\Entity\Burial;
use App\Entity\Company;
use App\Entity\Corpse;
use App\Entity\Model;
use App\Entity\ModelExtra;
use App\Entity\ModelMaterial;
use App\Entity\Order;
use App\Entity\Preparation;
use App\Entity\Theme;
use App\Form\AddressType;
use App\Form\CorpseType;
use App\Form\NewPreparationType;
use App\Repository\CompanyRepository;
use App\Repository\CompanyThemeRepository;
use App\Repository\CorpseRepository;
use App\Repository\ModelMaterialRepository;
use App\Repository\ModelRepository;
use App\Repository\OrderRepository;
use App\Repository\PaintingRepository;
use App\Repository\PreparationRepository;
use App\Repository\ThemeRepository;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted("ROLE_USER")]
class OrderController extends AbstractController
{

    /*
         * Order status :
         *  *NEW
         *  *DELIVERY_REACH
         *  *PROCESSING
         *  *SHIPPED
         *  *CLOSE
        */

    /*
     * Order type:
     *  *DRIVER
     *  *FUNERAL
     */


    #[Route('/commande', name: 'user_order', methods: ['GET'])]
    public function dashboard(OrderRepository $orderRepository): Response
    {

        $orderNotClose = $orderRepository->findAllOrderWithoutStatus('CLOSE');
        $orderClose = $orderRepository->findAllOrderWhenStatus('CLOSE');

        return $this->render('front/user/myCommand/index.html.twig', [
            'orderNotClose' => $orderNotClose,
            'orderClose' => $orderClose,
        ]);
    }

    #[Route('/commande/{id}', name: 'user_order_id', methods: ['GET'])]
    public function show(Order $order, int $id, PreparationRepository $preparationRepository): Response
    {

        if ($order->getPossessor() != $this->getUser()) {
            throw $this->createNotFoundException(
                'Aucune commande pour l\'id: ' . $id . ' vous appartient'
            );
        }

        $corpses = $order->getCorpses();
        foreach ($corpses as $corpse) {
            $corpse->setPreparation($preparationRepository->findOneBy(['corpse' => $corpse]));
        }

        return $this->render(
            'front/user/order.html.twig', [
                'order' => $order,
                'corpses' => $corpses
            ]
        );

    }

    /* DECLARE CORPSE */
    #[Route('/declarer-corps', name: 'declare_corpse', methods: ['POST', 'GET'])]
    public function declareCorpses(Request $request, ManagerRegistry $doctrine, OrderRepository $orderRep, CorpseRepository $corpseRep, Functions $functions): Response
    {
        $em = $doctrine->getManager();

        $corpseId = $request->request->get('corpseId');
        $corpse = $corpseRep->find($corpseId ?? -1) ?? new Corpse();
        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);

        $nextCorpse = false;

        // create form
        $form = $this->createForm(CorpseType::class, $corpse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // check data
            $corpse = $form->getData();

            if ($corpse->checkDateConsistency() && $corpse->isBirthdateValid()) {

                if (!$order) {

                    // create order
                    $order = new Order();
                    $order->setIsValid(false);
                    $order->setPossessor($this->getUser());
                    //TODO Check better number random string
                    $order->setNumber(Carbon::now()->getPreciseTimestamp(-2));
                    $order->setStatus(Order::DRAFT);

                    $corpse->setPosition(0);
                } else {

                    // get corpses
                    $corpses = $corpseRep->findBy(
                        ['command' => $order->getId()],
                        ['position' => 'ASC']
                    );

                    if (!$corpseId) $corpse->setPosition($corpses[count($corpses) - 1]->getPosition() + 1);
                }

                $corpse->setCommand($order);

                // persist
                $em->persist($corpse);
                $em->persist($order);
                $em->flush();

                if ($request->request->get('draftDeclaration')) {

                    $this->addFlash('success', 'Déclaration de corps bien enregistrée en tant que brouillon');
                    return $this->redirectToRoute('user_order');

                } else {

                    $corpses = $corpseRep->findBy(
                        ['command' => $order->getId()],
                        ['position' => 'ASC']
                    );

                    $indexCurrentCorpse = array_filter($corpses, fn($otherCorpse) => $otherCorpse->getPosition() === $corpse->getPosition()) ?? null;
                    $indexNextCorpse = is_array($indexCurrentCorpse) && !empty($indexCurrentCorpse) ? array_key_first($indexCurrentCorpse) + 1 : -1;

                    if ($indexNextCorpse) {
                        $corpse = $corpses[$indexNextCorpse] ?? new Corpse();
                        $nextCorpse = isset($corpses[$indexNextCorpse++]);
                    }

                    $form = $this->createForm(CorpseType::class, $corpse);
                    $this->addFlash('success', 'Corps bien ajouté');
                }
            } else {
                $this->addFlash('failed', 'Les dates ne sont pas coherent');
            }
        } else {
            if ($order) {
                $corpses = $corpseRep->findBy(
                    ['command' => $order->getId()],
                    ['position' => 'ASC']
                );

                $corpse = $corpses[0];
                $form = $this->createForm(CorpseType::class, $corpse);
                $nextCorpse = (bool)$corpses[1];
            }
        }

        return $this->renderForm('front/user/declareCorpse/index.html.twig', [
            'form' => $form,
            'order' => $order,
            'corpse' => $corpse,
            'nextCorpse' => $nextCorpse
        ]);
    }

    #[Route('/declare-corps-adresse', name: 'declare_corpse_address', methods: ['POST', 'GET'])]
    public function declareCorpsesAddress(Request $request, ManagerRegistry $doctrine, OrderRepository $orderRep, AddressOrderRepository $addressOrderRep): Response
    {
        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);
        $address = $addressOrderRep->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRAFT) ?? new Address();

        /* dump($order);
         dd($order->getAddressOrders()->toArray());*/
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();

            // insert address
            $addressOrder = new AddressOrder();
            $addressOrder->setAddress($address);
            $addressOrder->setCommand($order);

            $em->persist($addressOrder);
            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('declare_corpse_confirmation');

        }

        return $this->renderForm('front/user/declareCorpse/address.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/declare-corps-confirmation', name: 'declare_corpse_confirmation', methods: ['POST', 'GET'])]
    public function declareCorpsesConfirmation(Request $request, ManagerRegistry $doctrine, OrderRepository $orderRep): Response
    {
        $order = $orderRep->findOneBy(['status' => Order::DRAFT]);

        if ($order) {

            $em = $doctrine->getManager();

            if ($this->request->get('confirm') || $this->request->get('cancel')) {
                $this->request->get('confirm') ?? $order->setStatus(Order::DRIVER_NEW);
                $this->request->get('cancel') ?? $order->setStatus(Order::DRIVER_USER_CANCEL_ORDER);

            }
            dd('hello');

            return $this->renderForm('front/user/declareCorpse/confirmation.html.twig', [
                'order' => $order,
                'address' => $order->getAddressOrders()[0]->get
            ]);
        }

        $this->redirectToRoute('user_order');

    }

    /* ORDER SERVICE */
    #[Route('/commander-un-service/1', name: 'user_order_theme', methods: ['POST', 'GET'])]
    public function orderServiceTheme(Request $request, ThemeRepository $themeRepository, CorpseRepository $corpseRepository): Response
    {

        $themes = $themeRepository->findAll();
        $session = $request->getSession();

        if (is_numeric($request->query->get('corpse'))) {
            $cartSession['corpse'] = $request->query->get('corpse');
            $session->set('cartSession', $cartSession);

        }
        if ($request->query->get('nextStep') && $request->query->get('theme')) {
            $cartSession = $session->get('cartSession');
            $cartSession['theme'] = $request->query->get('theme');
            $session->set('cartSession', $cartSession);

            return $this->redirectToRoute('user_order_company', ['themeId' => $request->query->get('theme')]);
        }

        return $this->render('front/user/orderService/index.html.twig', [
            'themes' => $themes,
            'referer' => $request->headers->get('referer')

        ]);
    }

    #[Route('/commander-un-service/2', name: 'user_order_company', methods: ['POST', 'GET'])]
    public function orderServiceCompany(Request $request, CompanyThemeRepository $companyThemeRep, CompanyRepository $companyRepository, int $themeId = null): Response
    {
        $session = $request->getSession();

        if ($request->query->get('nextStep') && $request->query->get('company')) {
            $cartSession = $session->get('cartSession');
            $cartSession['company'] = $request->query->get('company');
            $session->set('cartSession', $cartSession);

            return $this->redirectToRoute('user_order_product');
        }

        $themeId = $request->query->get('themeId');
        $companies = $companyThemeRep->getCompaniesByTheme($themeId);

        return $this->render('front/user/orderService/companies.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/commander-un-service/3', name: 'user_order_product', methods: ['POST', 'GET'])]
    public function orderServiceProduct(Request $request, ModelMaterialRepository $modelMaterialRepository): Response
    {
        $session = $request->getSession();
        $cartSession = $session->get('cartSession');

        if ($request->query->get('nextStep') && $request->query->get('burial')) {
            $cartSession['burial'] = $request->query->get('burial');
            $session->set('cartSession', $cartSession);

            return $this->redirectToRoute('user_order_product_specificity');
        }

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find($cartSession['company']);
        $burials = $modelMaterialRepository->getBurialsByCompany($company);

        return $this->render('front/user/orderService/burials.html.twig', ['burials' => $burials]);
    }

    #[Route('/commander-un-service/4', name: 'user_order_product_specificity', methods: ['POST', 'GET'])]
    public function orderServiceProductSpecificity(Request $request, PaintingRepository $paintingRepository, ModelRepository $modelRepository): Response
    {
        $session = $request->getSession();
        $cartSession = $session->get('cartSession');

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find($cartSession['company']);
        $burial = $em->getRepository(Burial::class)->find($cartSession['burial']);
        $paintings = $paintingRepository->getByCompany($company);
        $models = $modelRepository->getByCompanyAndBurial($company, $burial);

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('model-item', $submittedToken) && $request->query->get('nextStep') && $request->query->get('model')) {
            $options = true;
            $material = $request->request->get('material');
            $extra = $request->request->get('extra');
            $model = $em->getRepository(Model::class)->find($request->query->get('model'));
            $modelExtra = NULL;

            if (!empty($extra)) {
                $modelExtra = $em->getRepository(ModelExtra::class)->findOneBy(['model' => $model, 'extra' => $extra]);
                if (empty($modelExtra)) $options = false;
            }

            if ($options) {

                $modelMaterial = $em->getRepository(ModelMaterial::class)->findOneBy(['model' => $model, 'material' => $material]);

                if (!empty($modelMaterial)) {

                    $cartSession = $session->get('cartSession');
                    $cartSession['model'] = $model->getId();
                    $cartSession['modelExtra'] = $modelExtra ? $modelExtra->getId() : NULL;
                    $cartSession['modelMaterial'] = $modelMaterial->getId();
                    $session->set('cartSession', $cartSession);

                    return $this->redirectToRoute('user_order_recap');
                }

            } else {
                $this->addFlash('error', 'Un soucis avec votre formulaire');
            }
        }

        return $this->render('front/user/orderService/products.html.twig', [
            'models' => $models,
            'paintings' => $paintings
        ]);
    }

    #[Route('/commander-un-service/recapitulatif', name: 'user_order_recap', methods: ['POST', 'GET'])]
    public function orderServiceRecap(Request $request): Response
    {
        $session = $request->getSession();
        $cartSession = $session->get('cartSession');
        $em = $this->getDoctrine()->getManager();
        $total = 0;

        $corpse = $em->getRepository(Corpse::class)->find($cartSession['corpse']);
        $theme = $em->getRepository(Theme::class)->find($cartSession['theme']);
        $company = $em->getRepository(Company::class)->find($cartSession['company']);
        $burial = $em->getRepository(Burial::class)->find($cartSession['burial']);
        $model = $em->getRepository(Model::class)->find($cartSession['model']);
        $modelExtra = $cartSession['modelExtra'] ? $em->getRepository(ModelExtra::class)->find($cartSession['modelExtra']) : null;
        $modelMaterial = $em->getRepository(ModelMaterial::class)->find($cartSession['modelMaterial']);

        /* ADDITION TOTAL */
        $total += $modelExtra ? $modelExtra->getExtra()->getPrice() : 0;
        $total += $theme->getPrice();
        $total += $model->getPrice();
        $total += $modelMaterial->getMaterial()->getPrice();

        if ($request->query->get('confirm')) {
            $preparation = new Preparation();
            $preparation->setPrice($total);
            $preparation->setCorpse($corpse);
            $preparation->setTheme($theme);
            $preparation->setModelMaterial($modelMaterial);
            $corpse->setPreparation($preparation);

            $em->persist($preparation);
            $em->flush();

            $session->remove('cartSession');

            return $this->redirectToRoute('user_order_success');
        }

        return $this->render('front/user/orderService/recap.html.twig', [
            'corpse' => $corpse,
            'theme' => $theme,
            'company' => $company,
            'burial' => $burial,
            'model' => $model,
            'extra' => $modelExtra ? $modelExtra->getExtra() : [],
            'material' => $modelMaterial->getMaterial(),
            'total' => $total
        ]);
    }

    #[Route('/commander-un-service/confirmation', name: 'user_order_success', methods: ['POST', 'GET'])]
    public function orderServiceSuccess(Request $request): Response
    {

        return $this->render('front/user/orderService/successOrder.html.twig');
    }
}
