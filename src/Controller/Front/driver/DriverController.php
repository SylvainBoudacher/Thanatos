<?php

namespace App\Controller\Front\driver;

use App\Entity\AddressOrder;
use App\Entity\Order;
use App\Form\ProcessingValidationType;
use App\Entity\DriverOrder;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Repository\CompanyRepository;
use App\Repository\DriverOrderRepository;
use App\Security\Voter\OrderVoter;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_DRIVER")]
#[Route("/conducteur/commandes")]
class DriverController extends AbstractController
{
    /**
     * @IsGranted("ROLE_DRIVER")
     */

    #[Route('/', name: 'driver_orders')]
    public function index(OrderRepository $orderRepository, CompanyRepository $companyRepository, DriverOrderRepository $driverOrderRepository, AddressRepository $addressRepository): Response
    {
        // get the driver id
        $company = $companyRepository->find($this->getUser()->getCompany());
        $ordersCorpseNew = $orderRepository->findAllOrderWhenTypeWhitStatus('DRIVER', 'DRIVER_NEW');
        $currentDriverOrder = $driverOrderRepository->findCurrentOrderDriverInProgress($company);
        $address = null;

        //if driver have orders
        if ($currentDriverOrder != null) {

            //get the addressOrder of the order
            $addressOrder = array_filter($currentDriverOrder->getCommand()->getAddressOrders()->toArray(),
                fn(AddressOrder $a) => $a->getStatus() === AddressOrder::DECLARATION_CORPSES);

            if (!empty($addressOrder)) $address = $addressOrder[0]->getAddress();

        }

        return $this->render('front/driver/orders/index.html.twig', [
            'controller_name' => 'DriverController',
            'ordersCorpseNew' => $ordersCorpseNew,
            'currentDriverOrder' => $currentDriverOrder,
            'address' => $address,
        ]);

    }

    #[Route('/prendre-commande/{id}', name: 'take_order')]
    public function takeOrder(OrderRepository $orderRepository, CompanyRepository $companyRepository, Order $order): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::TAKE_ORDER, $order);

        $company = $companyRepository->find($this->getUser()->getCompany());

        $entityManager = $this->getDoctrine()->getManager();
        $order->setStatus('DRIVER_ACCEPT');
        $entityManager->persist($order);
        $entityManager->flush();

        $driverOrder = new DriverOrder();
        $driverOrder->setDriver($company);
        $driverOrder->setCommand($order);
        $entityManager->persist($driverOrder);
        $entityManager->flush();

        return $this->redirectToRoute('my_order', ['id' => $order->getId()]);
    }

    #[Route('/traiter-commande/{id}', name: 'order_processing_corps')]
    public function processingOrder(OrderRepository $orderRepository, CompanyRepository $companyRepository, Order $order): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);
        if ($order->getStatus() != Order::DRIVER_ACCEPT) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('front/driver/orders/processing.html.twig', [
            'controller_name' => 'DriverController',
            'order' => $order,
        ]);
    }


    #[Route('/commande-arrive-au-client/{order_id}', name: 'order_arrive_to_client')]
    public function arriveDriverOrder(OrderRepository $orderRepository, $order_id): Response
    {

        $order = $orderRepository->find($order_id);

        $entityManager = $this->getDoctrine()->getManager();

        $order->setStatus('DRIVER_ARRIVES');

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('my_order', ['id' => $order->getId()]);
    }

    #[Route('/commande-valide/{id}', name: 'order_valid')]
    public function validOrder(OrderRepository $orderRepository, Order $order): Response
    {

        $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);
        if ($order->getStatus() != Order::DRIVER_ACCEPT) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $order->setStatus('DRIVER_PROCESSING_ACCEPT');

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('my_order', ['id' => $order->getId()]);
    }

    #[Route('/commande-invalide/{id}', name: 'order_invalid')]
    public function deleteInvalid(OrderRepository $orderRepository, Order $order): Response
    {

        $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);
        if ($order->getStatus() != Order::DRIVER_ACCEPT) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $order->setStatus('DRIVER_PROCESSING_REFUSED');
        $order->setDeletedAt(new DateTime());

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('driver_orders');
    }

    #[Route('/commande-stocke/{id}', name: 'order_stocked')]
    public function orderStocked(OrderRepository $orderRepository, Order $order): Response
    {

        $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);

        $entityManager = $this->getDoctrine()->getManager();

        $order->setStatus('DRIVER_CLOSE');

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('driver_orders');
    }

    #[Route('/commande/{id}', name: 'my_order')]
    public function myOrder(OrderRepository $orderRepository, CompanyRepository $companyRepository, Order $order): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);

        $address = null;
        $addressOrder = array_filter($order->getAddressOrders()->toArray(),
            fn(AddressOrder $a) => $a->getStatus() === AddressOrder::DECLARATION_CORPSES);

        if (!empty($addressOrder)) $address = $addressOrder[0]->getAddress();

        return $this->render('front/driver/orders/show.html.twig', [
            'order' => $order,
            'address' => $address
        ]);
    }
}
