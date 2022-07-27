<?php

namespace App\Security\Voter;

use App\Entity\AddressOrder;
use App\Entity\DriverOrder;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const TAKE_ORDER = 'TAKE_ORDER';
    public const CONFIRM = 'CONFIRM';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::TAKE_ORDER, self::CONFIRM])
            && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::TAKE_ORDER:
                return $this->canTakeOrder($subject, $user);
            case self::EDIT:
                return $this->canEdit($subject, $user);
                break;
            case self::CONFIRM:
                return $this->canConfirm();
                break;
        }

        return false;
    }

    private function canTakeOrder(Order $order, User $user): bool
    {

        $orderDriver = $this->em->getRepository(DriverOrder::class)->findOneBy(['command' => $order->getId()]);

        if ($orderDriver != null || $order->getStatus() != Order::DRIVER_NEW) return false;

        $company = $user->getCompany()->getId();
        $ordersDriver = $this->em->getRepository(DriverOrder::class)->findBy(['driver' => $company]);
        $ordersInProgress = array_filter($ordersDriver, fn($od) => in_array($od->getCommand()->getStatus(), [
            Order::DRIVER_ACCEPT,
            Order::DRIVER_ARRIVES,
            Order::DRIVER_PROCESSING_ACCEPT,
            Order::DRIVER_BRINGS_TO_WAREHOUSE
        ]));

        if (!empty($ordersInProgress)) return false;

        return true;
    }

    private function canEdit(Order $order, User $user): bool
    {
        $company = $user->getCompany()->getId();

        $orderDriver = $this->em->getRepository(DriverOrder::class)->findCurrentOrderDriverInProgressByCompanyAndOrder($company, $order);

        if ($orderDriver == null) return false;

        return true;
    }

    private function canConfirm(): bool
    {
        $order = $this->em->getRepository(Order::class)->findOneOwnedOrderByStatus(Order::DRAFT);
        $addressOrder = $this->em->getRepository(AddressOrder::class)->findOneOwnedByStatusAndOrder(AddressOrder::DECLARATION_CORPSES, Order::DRAFT, $order);

        if ($order == null || $addressOrder == null) return false;
        if (
            empty($order->getCorpses()->toArray()) ||
            $order->getTypes() != Order::DRIVER ||
            empty($order->getNumber()) ||
            $addressOrder->getAddress() == null ||
            $addressOrder->getAddress()->getNumber() == null ||
            $addressOrder->getAddress()->getCity() == null ||
            $addressOrder->getAddress()->getStreet() == null ||
            $addressOrder->getAddress()->getPostcode() == null
        ) return false;

        return true;
    }
}
