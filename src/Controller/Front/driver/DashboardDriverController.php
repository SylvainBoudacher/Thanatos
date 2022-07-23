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
#[Route("/conducteur")]
class DashboardDriverController extends AbstractController
{
    /**
     * @IsGranted("ROLE_DRIVER")
     */
    #[Route('/', name: 'dashboard_driver')]
    public function index(OrderRepository $orderRepository, CompanyRepository $companyRepository): Response
    {
        $company = $companyRepository->find($this->getUser()->getCompany());
        $getDriverOrders = $company->getDriverOrders();

        $currentOrder = null;

        if (!empty($getDriverOrders)) {
            foreach ($getDriverOrders as $driverOrder) {
                $driverOrders[] = $driverOrder->getCommand();
                foreach ($driverOrders as $order) {
                    if ($order->getStatus() !== 'DRIVER_NEW' || $order->getStatus() !== 'DRIVER_CLOSE') {
                        $currentOrder = $order;
                    }
                }
            }
        }

        return $this->render('front/driver/dashboard_driver/index.html.twig', [
            'currentOrder' => $currentOrder,
        ]);
    }
}
