<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface|User $user)
    {
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException("Veuillez valider votre email.");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}