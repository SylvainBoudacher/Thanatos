<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;



class StaticController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {

        if ($this->getUser() )
        {
            if ($this->getUser()->getRoles("ROLE_DRIVER")[0] == "ROLE_DRIVER")
            {
                return $this->redirectToRoute('dashboard_driver');

            } elseif ($this->getUser()->getRoles("ROLE_COMPANY")[0] == "ROLE_COMPANY") {
                return $this->redirectToRoute('home_company');

            } elseif ($this->getUser()->getRoles("ROLE_ADMIN")[0] == "ROLE_ADMIN") {
                return $this->redirectToRoute('admin_dashboard');
            } else
            {
                $orderNotClose = $orderRepository->findAllOrderWithoutStatus('CLOSE');
                $orderNotNew = $orderRepository->findAllOrderWithoutStatus('NEW');

                /*dd($orderNotClose[1]->getCorpses());*/

                return $this->render('front/index.html.twig', [
                    'controller_name' => 'StaticController',
                    'orderNotClose' => $orderNotClose,
                    'orderNotNew' => $orderNotNew,
                ]);


            }
        }
        else
        {
            return $this->render('front/index.html.twig', [
                'controller_name' => 'StaticController',
            ]);
        }
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('front/contact.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }

    #[Route('/a-propos', name: 'about')]
    public function about(): Response
    {
        return $this->render('front/about.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
}