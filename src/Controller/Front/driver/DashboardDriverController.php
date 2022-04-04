<?php

namespace App\Controller\Front\driver;

use App\Entity\DriverOrder;
use App\Repository\OrderRepository;
use App\Repository\CompanyRepository;
use App\Repository\DriverOrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_DRIVER")]
#[Route("/driver")]
class DashboardDriverController extends AbstractController
{
    /**
     * @IsGranted("ROLE_DRIVER")
    */
    #[Route('/', name: 'dashboard_driver')]
    public function index(): Response
    {
        return $this->render('front/driver/dashboard_driver/index.html.twig', [
            'controller_name' => 'DashboardDriverController',
        ]);
    }
}
