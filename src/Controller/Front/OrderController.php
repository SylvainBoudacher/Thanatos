<?php

namespace App\Controller\Front;

use App\Entity\Address;
use App\Entity\AddressOrder;
use App\Entity\Corpse;
use App\Entity\Order;
use App\Form\AddressType;
use App\Form\CorpseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

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
                    $this->addFlash('success', 'Corps bien ajouté');

                }
            } else {
                $this->addFlash('failed', 'Les dates ne sont pas coherents');
            }
        }

        return $this->renderForm('front/declareCorpse.html.twig', [
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
            $declareCorpses['addresses'][] = $address;
            $session->set('declareCorpses', $declareCorpses);

            if ($request->request->get('oneTime') !== null) {
                return $this->redirectToRoute('declare_corpse_confirmation');
            }

            $this->addFlash('failed', "L'adresse s'est bien ajoutée");
        }

        return $this->renderForm('front/declareCorpsesAddress.html.twig', [
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
        $addresses = $declareCorpses['addresses'];

        // declare corpses officially
        if ($request->query->get('confirmation') === true) {

            $entityManager = $doctrine->getManager();

            // create order
            $order = new Order();
            $order->setIsValid(false);
            // TODO : generate random number for order
            $order->setNumber("de");
            $order->setPossessor($this->getUser());

            // insert corpses
            foreach ($corpses as $corpse) {
                if ($corpse instanceof Corpse) {
                    $corpse->setCommand($order);
                    $entityManager->persist($corpse);
                }
            }

            // insert addresses
            foreach ($addresses as $address) {
                if ($address instanceof Address) {

                    // insert address-order
                    $addressOrder = new AddressOrder();
                    $addressOrder->setAddress($address);
                    $addressOrder->setCommand($order);

                    $entityManager->persist($addressOrder);
                    $entityManager->persist($address);

                }
            }
            $entityManager->persist($order);

            $entityManager->flush();
            $session->remove('declareCorpses');

            return $this->render('front/declareCorpsesSuccess.html.twig');
        }

        // cancel declare corpses
        if ($request->query->get('confirmation') === 'false') {
            $session->remove('declareCorpses');
            dd('redirect to dashboard');
        }

        return $this->renderForm('front/declareCorpsesConfirmation.html.twig', [
            'corpses' => $corpses,
            'addresses' => $addresses
        ]);
    }

}
