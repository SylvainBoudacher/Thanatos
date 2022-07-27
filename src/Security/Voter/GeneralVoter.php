<?php

namespace App\Security\Voter;

use App\Entity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GeneralVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const VIEW_EDIT = 'VIEW_EDIT';
    public const DELETE = 'DELETE';
    public const OWNED_COMPANY = 'OWNED_COMPANY';
    public const PREPARATION_INCLUDED = 'PREPARATION_INCLUDED';

    protected function supports(string $attribute, $subject): bool
    {

        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::VIEW_EDIT, self::OWNED_COMPANY, self::PREPARATION_INCLUDED, self::DELETE])
            && (
                $subject instanceof Entity\Model ||
                $subject instanceof Entity\Burial ||
                $subject instanceof Entity\Material ||
                $subject instanceof Entity\Extra ||
                $subject instanceof Entity\Painting ||
                $subject instanceof Entity\Theme ||
                $subject instanceof Entity\User
            );
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
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case self::VIEW_EDIT:
                return $this->canEditOrView($subject);
            case self::OWNED_COMPANY:
                return $this->isOwned($subject, $user);
            case self::PREPARATION_INCLUDED:
                return $this->isNotUsedInPreparation($subject);
            case self::DELETE:
                return $this->canDeleteTheme($subject);
                break;
        }

        return false;
    }

    private function canEditOrView($entity): bool
    {

        if ($entity->getDeletedAt()) return false;

        return true;
    }

    private function isOwned($entity, $user): bool
    {

        if ($user->getRoles()[0] == "ROLE_ADMIN") return true;
        else if ($entity->getCompany() === $user->getCompany()->getId()) return false;

        return true;
    }

    private function isNotUsedInPreparation($entity): bool
    {

        if (!empty($entity->getPreparations()->toArray())) return false;

        return true;
    }

    private function canDeleteTheme(Entity\Theme $entity): bool
    {

        if (!empty($entity->getCompanyThemes()->toArray())) return false;

        return true;
    }

}
