<?php

namespace App\Controller\Front\driver;

use App\Repository\OrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_DRIVER")]
#[Route("/driver")]
class DriverController extends AbstractController
{
    /**
     * @IsGranted("ROLE_DRIVER")
    */
    #[Route('/', name: 'driver_dashboard')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAllNewOrder();

        return $this->render('front/driver/index.html.twig', [
            'controller_name' => 'DriverController',
            'orders' => $orders,
        ]);
    }
}
