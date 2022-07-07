<?php

namespace App\Security\Voter;

use App\Entity\Corpse;
use App\Entity\Order;
use App\Entity\Preparation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PreparationVoter extends Voter
{

    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const ORDER = 'ORDER';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function supports(string $attribute, $subject): bool
    {

        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::ORDER])
            && ($subject instanceof Corpse ||
                $subject instanceof Preparation);

        return in_array($attribute, [self::EDIT, self::VIEW, self::ORDER]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                break;
            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case self::ORDER:
                return $this->canOrder($subject, $user);
                break;
        }

        return false;
    }

    private function canOrder(Corpse $corpse, User $user): bool
    {

        if ($corpse->getDeletedAt()) return false;

        // Get current order and check if order is owned by the user
        $order = $this->em->getRepository(Order::class)->findOneBy([
            'possessor' => $user,
            'status' => Order::DRIVER_CLOSE,
            'id' => $corpse->getCommand()->getId(),
            'deletedAt' => null
        ]);

        if (!$order) return false;

        // if preparation exist check if status is draft
        if ($corpse->getPreparation()) {

            if (!($corpse->getPreparation()->getStatus() === Preparation::FUNERAL_DRAFT && $corpse->getPreparation()->getDeletedAt() === null)) return false;
        }

        return true;
    }
}
