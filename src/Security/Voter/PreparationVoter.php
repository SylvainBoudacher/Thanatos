<?php

namespace App\Security\Voter;

use App\Entity\Corpse;
use App\Entity\Order;
use App\Entity\Preparation;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PreparationVoter extends Voter
{

    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const ORDER = 'ORDER';
    public const ORDER_CLASSIC = 'ORDER_CLASSIC';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function supports(string $attribute, $subject): bool
    {

        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::ORDER, self::ORDER_CLASSIC])
            && ($subject instanceof Corpse ||
                $subject instanceof Preparation);

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
            case self::EDIT:
                return $this->canEdit($subject, $user);
                break;
            case self::VIEW:
                return $this->canView($subject, $user);
                break;
            case self::ORDER:
                return $this->canOrder($subject, $user);
            case self::ORDER_CLASSIC:

                return $this->canOrderTypeClassic($subject);
        }

        return false;
    }

    private function canEdit(Preparation $preparation, User $user): bool
    {

        if ($preparation->getDeletedAt() != null) return false;

        return $preparation->getModelMaterial()->getModel()->getCompany()->getId() === $user->getCompany()->getId() &&
            $preparation->getStatus() != Preparation::FUNERAL_CLOSE_PROCESSING &&
            $preparation->getStatus() != Preparation::FUNERAL_CANCEL;

    }

    private function canView(Preparation $preparation, User $user): bool
    {

        if ($preparation->getDeletedAt() != null) return false;

        return $preparation->getModelMaterial()->getModel()->getCompany()->getId() === $user->getCompany()->getId();

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

    private function canOrderTypeClassic(Corpse $corpse): bool
    {

        if ($corpse->getPreparation() == null || $corpse->getPreparation()->getTheme() == null) return false;

        if ($corpse->getPreparation()->getTheme()->getType() !== Theme::TYPE_CLASSIC) return false;

        return true;
    }

}
