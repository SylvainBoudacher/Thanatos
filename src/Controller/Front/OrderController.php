<?php

namespace App\Controller\Front;

use App\Entity\Corpse;
use App\Form\CorpseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class OrderController extends AbstractController
{

    #[Route('/declarer-un-corps', name: 'declare_corpse', methods: ['POST', 'GET'])]
    public function declareCorpse(Request $request, ManagerRegistry $doctrine): Response
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

                $items = $session->get('items', []);
                $items[] = $corpse;
                $session->set('items', $items);

                // save all corpses at once when user finished
                if($request->request->get('oneCorpse') !== null){

                    $items = $session->get('items', []);
                    $entityManager = $doctrine->getManager();

                    foreach ($items as $item)
                    {
                        if($item instanceof Corpse) $entityManager->persist($item);
                    }
                    $entityManager->flush();
                    $session->remove('items');


                }

            } else {
                $this->addFlash('failed', 'Les dates ne sont pas coherents');
            }
        }

        return $this->renderForm('front/declareCorpse.html.twig', [
            'form' => $form,
        ]);
    }
}
