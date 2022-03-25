<?php

namespace App\Controller\Front\user;

use App\Entity\Address;
use App\Entity\AddressOrder;
use App\Entity\CompanyTheme;
use App\Entity\Corpse;
use App\Entity\Order;
use App\Form\AddressType;
use App\Form\CorpseType;
use App\Repository\CompanyRepository;
use App\Repository\CompanyThemeRepository;
use App\Repository\CorpseRepository;
use App\Repository\OrderRepository;
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

            return $this->redirectToRoute('user_order_product', ['companyId' => $request->query->get('company') ]);
        }

        $themeId = $request->query->get('themeId');
        $companies = $companyThemeRep->getCompaniesByTheme($themeId);

        return $this->render('front/user/orderService/companies.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/commander-un-service/3', name: 'user_order_product', methods: ['POST', 'GET'])]
    public function orderServiceProduct(Request $request, int $companyId = null): Response
    {
        $session = $request->getSession();
        dump($session->all());
        dd('hello');
        return $this->render('front/user/orderService/products.html.twig', [
            'companies' => $companies,
        ]);
    }
}
