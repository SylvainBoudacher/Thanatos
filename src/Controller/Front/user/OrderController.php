<?php

namespace App\Controller\Front\user;

use App\Entity\CompanyPainting;
use App\Entity\Extra;
use App\Entity\Preparation;
use App\Entity\Theme;
use App\Repository\AddressOrderRepository;
use App\Entity\Address;
use App\Entity\AddressOrder;
use App\Entity\Burial;
use App\Entity\Company;
use App\Entity\Corpse;
use App\Entity\Model;
use App\Entity\ModelExtra;
use App\Entity\ModelMaterial;
use App\Entity\Order;
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
use PHPUnit\Util\Color;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
class OrderController extends AbstractController
{

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
    public function declareCorpses(Request $request, ManagerRegistry $doctrine, OrderRepository $orderRep, CorpseRepository $corpseRep): Response
    {
        $em = $doctrine->getManager();

        $corpseId = $request->request->get('corpseId');
        $corpse = is_numeric($corpseId) ? $corpseRep->find($corpseId) : new Corpse();  //TODO check if this verification is enough
        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);

        $nextCorpse = false;

        // if corpse exist, check if it's owned
        if ($corpse instanceof Corpse && $corpse->getId()) {
            if (!$order) dd('crash error : no corpse exist without order');
            $corpseIsOwned = $order->getId() == $corpse->getCommand()->getId();
            if (!$corpseIsOwned) dd('crash error : no corpse exist without order');
        }
        // create form
        $form = $this->createForm(CorpseType::class, $corpse);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
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
                        $order->setTypes(Order::DRIVER);

                        $corpse->setPosition(0);
                    } else {
                        // get corpses
                        $corpses = $corpseRep->findBy(
                            ['command' => $order->getId()],
                            ['position' => 'ASC']
                        );

                        if (!$corpse->getId()) $corpse->setPosition($corpses[count($corpses) - 1]->getPosition() + 1);
                    }
                    $corpse->setCommand($order);
                    $order->addCorpse($corpse);

                    // persist
                    $em->persist($corpse);
                    $em->persist($order);
                    $em->flush();

                    $order = $orderRep->find($order->getId());

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
            }

        } else {
            if ($order) {
                $corpses = $corpseRep->findBy(
                    ['command' => $order->getId()],
                    ['position' => 'ASC']
                );
                $corpse = $corpses[0];
                $form = $this->createForm(CorpseType::class, $corpse);
                $nextCorpse = isset($corpses[1]);
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
        $address = new Address();

        // get address if already exist
        $addressOrder = $addressOrderRep->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRAFT);
        if ($addressOrder instanceof AddressOrder) $address = $addressOrder->getAddress();
        else $addressOrder = new AddressOrder();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $address = $form->getData();

            // insert address
            $addressOrder->setAddress($address);
            $addressOrder->setCommand($order);
            $addressOrder->setStatus(AddressOrder::DECLARATION_CORPSES);

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
    public function declareCorpsesConfirmation(
        Request                $request,
        ManagerRegistry        $doctrine,
        OrderRepository        $orderRep,
        AddressOrderRepository $addressOrderRep,
    ): Response
    {
        $order = $orderRep->findOneBy(['status' => Order::DRAFT]);
        $addressOrder = $addressOrderRep->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRAFT);

        if ($order && $addressOrder) {

            $address = $addressOrder->getAddress();
            $em = $doctrine->getManager();

            if ($request->query->get('confirm') === 'true') {
                if ($request->query->get('confirm')) {
                    /*$order->setStatus(Order::DRIVER_NEW);*/
                    // Driver dois payer la commande
                    return $this->redirectToRoute('user_order_payment');
                }

                if ($request->query->get('cancel')) {
                    $order->setStatus(Order::DRIVER_USER_CANCEL_ORDER);
                    $this->addFlash('error', "Votre déclaration de corps s'est bien annulée");
                }
                $em->persist($order);
                $em->flush();

                return $this->redirectToRoute('user_order');
            }

            return $this->renderForm('front/user/declareCorpse/confirmation.html.twig', [
                'order' => $order,
                'address' => $address
            ]);
        }

        return $this->redirectToRoute('user_order');
    }

    #[Route('/supprimer-corps-declaration/{id}', name: 'delete_corpse_declaration')]
    public function delete_corpse_declaration(Corpse $corpse, OrderRepository $orderRep)
    {

        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);

        if (array_filter($order->getCorpses()->toArray(), fn($c) => $c->getId() == $corpse->getId())) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($corpse);
            $em->flush();
        }

        return $this->redirectToRoute('declare_corpse_confirmation');
    }

    #[Route('/modifier-corps-declaration/{id}', name: 'edit_corpse_declaration')]
    public function edit_corpse_declaration(Request $request, Corpse $corpse, OrderRepository $orderRep)
    {
        $order = $orderRep->findOneOwnedOrderByStatus(Order::DRAFT);

        if (array_filter($order->getCorpses()->toArray(), fn($c) => $c->getId() == $corpse->getId())) {

            $form = $this->createForm(CorpseType::class, $corpse);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($corpse);
                $em->flush();

                return $this->redirectToRoute('declare_corpse_confirmation');
            }

            return $this->renderForm('front/user/declareCorpse/index.html.twig', [
                'form' => $form,
                'order' => $order,
                'corpse' => $corpse,
                'nextCorpse' => false,
                'editVersion' => true
            ]);

        }

        return $this->redirectToRoute('declare_corpse_confirmation');
    }

    /* ORDER SERVICE */
    #[Route('/commander-un-service/etape-1/{corpse}/{theme}', name: 'user_order_theme', methods: ['POST', 'GET'])]
    public function orderServiceTheme(Request $request, ThemeRepository $themeRepository, CorpseRepository $corpseRepository, OrderRepository $orderRepository, Corpse $corpse, Theme $theme = null): Response
    {

        $preparation = new Preparation();

        // Get current order and check if order is owned by the user
        $order = $orderRepository->findOneBy([
            'possessor' => $this->getUser(),
            'status' => Order::DRIVER_CLOSE,
            'id' => $corpse->getCommand()->getId()
        ]);

        if (!$order) dd('error : corpse not owned');

        if ($corpse->getPreparation()) {
            if ($corpse->getPreparation()->getStatus() === Preparation::FUNERAL_DRAFT) $preparation = $corpse->getPreparation();
            else dd("error : your current preparation is not draft");
        }

        if ($theme) {

            $preparation->setTheme($theme);
            $preparation->setStatus(Preparation::FUNERAL_DRAFT);
            $preparation->setCorpse($corpse);
            $preparation->setCommand($corpse->getCommand());
            $corpse->setPreparation($preparation);

            $em = $this->getDoctrine()->getManager();
            $em->persist($preparation);
            $em->flush();

            return $this->redirectToRoute('user_order_company', ['id' => $corpse->getId()]);
        }

        $themesSpecial = $themeRepository->findBy(['type' => Theme::TYPE_SPECIAL], ['name' => 'ASC']);
        $themesClassic = $themeRepository->findBy(['type' => Theme::TYPE_CLASSIC], ['name' => 'ASC']);

        return $this->render('front/user/orderService/index.html.twig', [
            'themesSpecial' => $themesSpecial,
            'themesClassic' => $themesClassic,
            'corpse' => $corpse,
        ]);
    }

    #[Route('/commander-un-service/etape-2/{id}', name: 'user_order_company', methods: ['POST', 'GET'])]
    public function orderServiceCompany(Request $request, CompanyThemeRepository $companyThemeRep, CompanyRepository $companyRepository, ModelMaterialRepository $modelMaterialRepository, ModelRepository $modelRepository, OrderRepository $orderRepository, PreparationRepository $preparationRepository, Corpse $corpse): Response
    {

        // Get current order adn check if order is owned by the user
        $order = $orderRepository->findBy([
            'possessor' => $this->getUser(),
            'status' => Order::DRIVER_CLOSE,
            'id' => $corpse->getCommand()->getId()
        ]);

        if (!$order) dd('error : corpse not owned');
        if (!($corpse->getPreparation() && $corpse->getPreparation()->getStatus() === Preparation::FUNERAL_DRAFT)) dd('error : preparation not owned');

        $companies = $modelRepository->getCompaniesThatHaveModels();

        return $this->render('front/user/orderService/companies.html.twig', [
            'companies' => $companies,
            'corpse' => $corpse
        ]);
    }

    #[Route('/commander-un-service/etape-3/{corpse}/pompe-funebre/{company}', name: 'user_order_product', methods: ['POST', 'GET'])]
    public function orderServiceProduct(Request $request, ModelRepository $modelRepository, Corpse $corpse, Company $company): Response
    {

        $data = $modelRepository->getCompleteProductsRelatedToCompany($company);
        $burials = array_filter($data, fn($i) => $i instanceof Burial);
        $models = array_filter($data, fn($i) => $i instanceof Model);
        $modelsExtra = array_filter($data, fn($i) => $i instanceof ModelExtra);
        $modelsMaterial = array_filter($data, fn($i) => $i instanceof ModelMaterial);
        $companiesPainting = array_filter($data, fn($i) => $i instanceof CompanyPainting);

        return $this->render('front/user/orderService/company.html.twig', [
            'company' => $company,
            'burials' => $burials,
            'models' => $models,
            'modelsMaterial' => $modelsMaterial,
            'companiesPainting' => $companiesPainting,
            'modelsExtra' => $modelsExtra,
            'corpse' => $corpse
        ]);
    }

    #[Route('/commander-un-service/recapitulatif/{corpse}', name: 'user_order_recap', methods: ['POST', 'GET'])]
    public function temp(Request $request, PaintingRepository $paintingRepository, ModelRepository $modelRepository, Corpse $corpse): Response
    {
        dump($corpse);

        dd($request->request->all());
    }

//    #[Route('/commander-un-service/recapitulatif', name: 'user_order_recap', methods: ['POST', 'GET'])]
    /* public function orderServiceRecap(Request $request): Response
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
     }*/

    /**
     * @throws ApiErrorException
     */
    #[Route('/commander-un-service/payement', name: 'user_order_payment', methods: ['POST', 'GET'])]
    public function orderServicePayement(Request $request): Response
    {

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'submit_type' => 'pay',
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => 'price_1LH6P2GgCa17kbBHIR6gSl2k',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost' . $this->generateUrl('user_order_success'),
            'cancel_url' => 'http://localhost' . $this->generateUrl('user_order_cancel'),
        ]);

        header("Location: " . $checkout_session->url);

        return $this->render('front/user/payment/payment.html.twig', [
            'checkout_session' => $checkout_session,
        ]);

    }

    #[Route('/commander-un-service/payement/confirmation', name: 'user_order_success', methods: ['POST', 'GET'])]
    public function orderServiceSuccess(Request $request, OrderRepository $orderRep,): Response
    {
        $em = $this->getDoctrine()->getManager();
        $order = $orderRep->findOneBy(['status' => Order::DRAFT]);
        $order->setStatus(Order::DRIVER_NEW);
        $em->persist($order);
        $em->flush();

        return $this->render('front/user/payment/success.html.twig');
    }

    #[Route('/commander-un-service/payement/annulation', name: 'user_order_cancel', methods: ['POST', 'GET'])]
    public function orderServiceCancel(Request $request): Response
    {
        return $this->render('front/user/payment/cancel.html.twig');
    }
}
