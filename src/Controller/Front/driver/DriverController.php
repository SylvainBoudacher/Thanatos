<?php

namespace App\Controller\Front\driver;

use App\Entity\AddressOrder;
use App\Entity\Company;
use App\Entity\Order;
use App\Entity\Preparation;
use App\Form\ProcessingValidationType;
use App\Entity\DriverOrder;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Repository\CompanyRepository;
use App\Repository\DriverOrderRepository;
use App\Repository\PreparationRepository;
use App\Security\Voter\OrderVoter;
use App\Security\Voter\PreparationVoter;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(OrderRepository $orderRepository, PreparationRepository $preparationRepository, CompanyRepository $companyRepository, DriverOrderRepository $driverOrderRepository, AddressRepository $addressRepository): Response
    {
        // get the driver id
        $company = $companyRepository->find($this->getUser()->getCompany());
        $ordersCorpseNew = $orderRepository->findAllOrderWhenTypeWhitStatus('DRIVER', 'DRIVER_NEW');
        $ordersServiceNew = $preparationRepository->getPreparationsNewNotOccupied();
        $currentDriverOrder = $driverOrderRepository->findCurrentOrderDriverInProgress($company);
        $currentPreparation = $preparationRepository->findCurrentOrderDriverInProgress($company);

        $address = null;
        $idOrder = null;

        //if driver have orders
        if ($currentDriverOrder != null) {

            //get the addressOrder of the order
            $addressOrder = array_filter($currentDriverOrder->getCommand()->getAddressOrders()->toArray(),
                fn(AddressOrder $a) => $a->getStatus() === AddressOrder::DECLARATION_CORPSES);

            if (!empty($addressOrder)) $address = $addressOrder[0]->getAddress();

            $idOrder = $currentDriverOrder->getCommand()->getId();

            //if driver has preparation course
        } elseif ($currentPreparation != null) {

            $currentDriverOrder = $currentPreparation;

            $address = $currentDriverOrder->getModelExtra()->getModel()->getCompany()->getAddress();
            $idOrder = $currentDriverOrder->getId();

        }

        return $this->render('front/driver/orders/index.html.twig', [
            'controller_name' => 'DriverController',
            'ordersCorpseNew' => $ordersCorpseNew,
            'currentDriverOrder' => $currentDriverOrder,
            'ordersServiceNew' => $ordersServiceNew,
            'address' => $address,
            'idOrder' => $idOrder
        ]);

    }

    #[Route('/prendre-commande/{id}', name: 'take_order')]
    public function takeOrder(EntityManagerInterface $em, Order $order): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::TAKE_ORDER, $order);

        $company = $em->getRepository(Company::class)->find($this->getUser()->getCompany());
        $currentDriverOrder = $em->getRepository(DriverOrder::class)->findCurrentOrderDriverInProgress($company);
        $currentPreparation = $em->getRepository(Preparation::class)->findCurrentOrderDriverInProgress($company);

        if ($currentDriverOrder != null ||
            $currentPreparation != null) throw $this->createAccessDeniedException();

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

    #[Route('/prendre-preparation/{id}', name: 'take_preparation')]
    public function takePreparation(PreparationRepository $preparationRepository, EntityManagerInterface $em, Preparation $preparation): Response
    {


        $company = $em->getRepository(Company::class)->find($this->getUser()->getCompany());
        $currentDriverOrder = $em->getRepository(DriverOrder::class)->findCurrentOrderDriverInProgress($company);
        $currentPreparation = $preparationRepository->findCurrentOrderDriverInProgress($company);

        if ($currentDriverOrder != null ||
            $currentPreparation != null) throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted(PreparationVoter::TAKE_ORDER, $preparation);

        if ($preparation->getStatus() == Preparation::FUNERAL_ACCEPT) $preparation->setStatus('FUNERAL_DRIVER_ACCEPT_TO_BRINGS_TO_FUNERAL');
        elseif ($preparation->getStatus() == Preparation::FUNERAL_CLOSE_PROCESSING) $preparation->setStatus('FUNERAL_DRIVER_ACCEPT_BRINGS_TO_USER');
        else throw $this->createAccessDeniedException();

        $preparation->setDriver($company);
        $em->flush();

        return $this->redirectToRoute('my_order', ['id' => $preparation->getId()]);
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

    #[Route('/commande-arrive-a-la-morgue/{id}', name: 'order_arrive_to_company')]
    public function arriveToCompany(EntityManagerInterface $em, OrderRepository $orderRepository, Preparation $preparation): Response
    {
        $company = $em->getRepository(Company::class)->find($this->getUser()->getCompany());

        if (
            $preparation->getStatus() != Preparation::FUNERAL_DRIVER_ACCEPT_TO_BRINGS_TO_FUNERAL ||
            $preparation->getDriver() != $company
        ) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $preparation->setStatus('FUNERAL_DRIVER_BRINGS_TO_FUNERAL');
        $preparation->setDriver(null);
        $entityManager->flush();

        return $this->redirectToRoute('driver_orders');
    }

    #[Route('/commande-arrive-chez-le-client/{id}', name: 'order_arrive_to_client')]
    public function arriveToClient(EntityManagerInterface $em, OrderRepository $orderRepository, Preparation $preparation): Response
    {
        $company = $em->getRepository(Company::class)->find($this->getUser()->getCompany());

        if (
            $preparation->getStatus() != Preparation::FUNERAL_DRIVER_ACCEPT_BRINGS_TO_USER ||
            $preparation->getDriver() != $company
        ) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $preparation->setStatus('FUNERAL_DRIVER_CLOSE_BRING');
        $preparation->setDriver(null);
        $entityManager->flush();

        return $this->redirectToRoute('driver_orders');
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
    public function myOrder(EntityManagerInterface $em, OrderRepository $orderRepository, CompanyRepository $companyRepository, int $id): Response
    {
        $company = $em->getRepository(Company::class)->find($this->getUser()->getCompany());
        $currentDriverOrder = $em->getRepository(DriverOrder::class)->findCurrentOrderDriverInProgress($company);
        $currentPreparation = $em->getRepository(Preparation::class)->findCurrentOrderDriverInProgress($company);

        if ($currentDriverOrder != null) {
            $order = $em->getRepository(Order::class)->find($id);
            $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);

            $currentDriverOrder->address = null;
            $addressOrder = array_filter($order->getAddressOrders()->toArray(),
                fn(AddressOrder $a) => $a->getStatus() === AddressOrder::DECLARATION_CORPSES);

            if (!empty($addressOrder)) $currentDriverOrder->address = $addressOrder[0]->getAddress();

        } else if ($currentPreparation != null) {
            $currentPreparation->address = $currentPreparation->getModelExtra()->getModel()->getCompany()->getAddress();
        } else {
            throw $this->createAccessDeniedException();
        }

        return $this->render('front/driver/orders/show.html.twig', [
            'currentDriverOrder' => $currentDriverOrder,
            'currentPreparation' => $currentPreparation
        ]);
    }
}
