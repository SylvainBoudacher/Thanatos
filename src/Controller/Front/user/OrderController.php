<?php

namespace App\Controller\Front\user;

use App\Entity\Address;
use App\Entity\AddressOrder;
use App\Entity\Burial;
use App\Entity\Company;
use App\Entity\CompanyExtra;
use App\Entity\CompanyPainting;
use App\Entity\Corpse;
use App\Entity\Extra;
use App\Entity\Material;
use App\Entity\Model;
use App\Entity\ModelExtra;
use App\Entity\ModelMaterial;
use App\Entity\Order;
use App\Entity\Preparation;
use App\Entity\Theme;
use App\Form\AddressType;
use App\Form\CorpseType;
use App\Form\NewPreparationType;
use App\Repository\CompanyPaintingRepository;
use App\Repository\CompanyRepository;
use App\Repository\CompanyThemeRepository;
use App\Repository\CorpseRepository;
use App\Repository\ModelExtraRepository;
use App\Repository\ModelMaterialRepository;
use App\Repository\ModelRepository;
use App\Repository\OrderRepository;
use App\Repository\PaintingRepository;
use App\Repository\ThemeRepository;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted("ROLE_USER")]
class OrderController extends AbstractController
{

    #[Route('/commande', name: 'user_order', methods: ['GET'])]
    public function dashboard(OrderRepository $orderRepository): Response {

        $order = $orderRepository->findMyCurrentOrder($this->getUser()->getId()); // get current order
        $orders = $orderRepository->findLastFinishedLimitByUser($this->getUser()->getId()); // get finished order

        return $this->render('front/user/index.html.twig', [
            'order' => $order,
            'orders' => $orders
        ]);

    }

    #[Route('/commande/{id}', name: 'user_order_id', methods: ['GET'])]
    public function show(Order $order, int $id): Response {


        if ($order->getPossessor() != $this->getUser()) {
            throw $this->createNotFoundException(
                'Aucune commande pour l\'id: ' . $id . ' vous appartient'
            );
        }

        return $this->render(
            'front/user/order.html.twig', [
                'order' => $order
            ]
        );

    }

    /* DECLARE CORPSE */

    #[Route('/declarer-corps', name: 'declare_corpse', methods: ['POST', 'GET'])]
    public function declareCorpses(Request $request): Response
    {
        // create form
        $corpse = new Corpse();
        $form = $this->createForm(CorpseType::class, $corpse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // check data
            $corpse = $form->getData();

            if ($corpse->checkDateConsistency() && $corpse->isBirthdateValid()) {

                // save each corpse in session
                $session = $request->getSession();

                $declareCorpses = $session->get('declareCorpses', []);
                $declareCorpses['corpses'][] = $corpse;
                $session->set('declareCorpses', $declareCorpses);

                // save all corpses at once when user finished
                if ($request->request->get('oneCorpse') !== null) {

                    return $this->redirectToRoute('declare_corpse_address');
                } else {

                    $corpse = new Corpse();
                    $form = $this->createForm(CorpseType::class, $corpse);
                    $this->addFlash('success', 'Corps bien ajouté');
                }
            } else {
                $this->addFlash('failed', 'Les dates ne sont pas coherents');
            }
        }else {
            if ($request->request->get('oneCorpse') !== null) {
                return $this->redirectToRoute('declare_corpse_address');
            }
        }

        return $this->renderForm('front/user/declareCorpse/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/declare-corps-adresse', name: 'declare_corpse_address', methods: ['POST', 'GET'])]
    public function declareCorpsesAddress(Request $request): Response
    {

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();

            $declareCorpses = $session->get('declareCorpses', []);
            $declareCorpses['address'] = $address;
            $session->set('declareCorpses', $declareCorpses);

            return $this->redirectToRoute('declare_corpse_confirmation');

        }

        return $this->renderForm('front/user/declareCorpse/address.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/declare-corps-confirmation', name: 'declare_corpse_confirmation', methods: ['POST', 'GET'])]
    public function declareCorpsesConfirmation(Request $request, ManagerRegistry $doctrine): Response
    {
        $session = $request->getSession();
        $declareCorpses = $session->get('declareCorpses');
        if ($declareCorpses === null || !isset($declareCorpses['corpses'])) {
            $session->remove('declareCorpses');
            return $this->redirectToRoute('homepage');
        }
        $corpses = $declareCorpses['corpses'];
        $address = $declareCorpses['address'];

        // declare corpses officially
        if ($request->query->get('confirmation') === 'true') {

            $entityManager = $doctrine->getManager();

            // create order
            $order = new Order();
            $order->setIsValid(false);
            $order->setPossessor($this->getUser());

            // insert corpses
            foreach ($corpses as $corpse) {
                if ($corpse instanceof Corpse) {
                    $corpse->setCommand($order);
                    $entityManager->persist($corpse);
                }
            }

            // insert addresses
            if ($address instanceof Address) {

                // insert address-order
                $addressOrder = new AddressOrder();
                $addressOrder->setAddress($address);
                $addressOrder->setCommand($order);

                $entityManager->persist($addressOrder);
                $entityManager->persist($address);

            }
            $entityManager->persist($order);
            $order->setNumber($order->getId() . Carbon::now()->format('Ymd'));
            $order->setStatus(Order::NEW);

            $entityManager->flush();
            $session->remove('declareCorpses');

            return $this->render('front/user/declareCorpse/success.html.twig');
        }

        // cancel declare corpses
        if ($request->query->get('confirmation') === 'false') {
            $session->remove('declareCorpses');

            $this->redirectToRoute('user_order');
        }

        return $this->renderForm('front/user/declareCorpse/confirmation.html.twig', [
            'corpses' => $corpses,
            'address' => $address
        ]);
    }

    /* ORDER SERVICE */
    #[Route('/commander-un-service/1', name: 'user_order_theme', methods: ['POST', 'GET'])]
    public function orderServiceTheme(Request $request, ThemeRepository $themeRepository, CorpseRepository $corpseRepository): Response
    {

        $themes = $themeRepository->findAll();
        $session = $request->getSession();

        if(is_numeric($request->query->get('corpse'))){
            $cartSession['corpse'] = $request->query->get('corpse');
            $session->set('cartSession', $cartSession);

        }
        if($request->query->get('nextStep') && $request->query->get('theme'))
        {
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

        if($request->query->get('nextStep') && $request->query->get('company'))
        {
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

        if($request->query->get('nextStep') && $request->query->get('burial'))
        {
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

        if($this->isCsrfTokenValid('model-item', $submittedToken) && $request->query->get('nextStep') && $request->query->get('model'))
        {
            $extraGood = true;
            $material = $request->request->get('material');
            $extras = $request->request->get('extras');
            $model = $em->getRepository(Model::class)->find($request->query->get('model'));
            $modelExtras = [];

            if(!empty($extras)){
                foreach ($extras as $extra)
                {
                    $modelExtra = $em->getRepository(ModelExtra::class)->findOneBy(['model' => $model, 'extra' => $extra]);
                    if(!empty($modelExtra)) $modelExtras[] = $modelExtra->getId();
                    else $extraGood = false;
                }
            }

            $modelMaterial = $em->getRepository(ModelMaterial::class)->findOneBy(['model' => $model, 'material' => $material]);

            if(!$extraGood || empty($modelMaterial) )
            {
                $this->addFlash('failed', 'Les valeurs ne sont pas corrects');

            }else {

                $cartSession = $session->get('cartSession');
                $cartSession['model'] = $model->getId();
                $cartSession['modelExtras'] = $modelExtras;
                $cartSession['modelMaterial'] = $modelMaterial->getId();
                $session->set('cartSession', $cartSession);

                return $this->redirectToRoute('user_order_recap');
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
        $extras = $em->getRepository(Extra::class)->findBy([ 'id' => $cartSession['modelExtras']]);
        $modelMaterial = $em->getRepository(Material::class)->find($cartSession['modelMaterial']);

        /* ADDITION TOTAL */
        foreach ($extras as $extra)
        {
            $total += $extra->getPrice();
        }
        $total += $theme->getPrice();
        $total += $model->getPrice();
        $total += $modelMaterial->getPrice();

        if($request->query->get('confirm'))
        {
            $preparation = new Preparation();
            $preparation->setPrice($total);
            $preparation->setCorpse($corpse);
            $preparation->setTheme($theme);
            $preparation->setModelMaterial($painting);

            return $this->redirectToRoute('user_order_product_specificity');
        }

        return $this->render('front/user/orderService/recap.html.twig', [
            'corpse' => $corpse,
            'theme' => $theme,
            'company' => $company,
            'burial' => $burial,
            'model' => $model,
            'extras' => $extras,
            'modelMaterial' => $modelMaterial,
            'total' => $total
        ]);
    }
}
