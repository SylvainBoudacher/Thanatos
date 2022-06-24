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

            } elseif ($this->getUser()->getRoles("ROLE_MORGUE")[0] == "ROLE_MORGUE") {
                return $this->redirectToRoute('morgue_dashboard');

            } else
            {
                $orderTRy = $orderRepository->findAllOrderWhenTypeIsType('DRIVER');

                return $this->render('front/index.html.twig', [
                    'controller_name' => 'StaticController',
                    'orderTRy' => $orderTRy,
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