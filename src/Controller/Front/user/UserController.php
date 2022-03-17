<?php

namespace App\Controller\Front\user;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    #[Route('/account', name: 'app_settings_account')]
    public function account(): Response
    {
        return $this->render('front/user/settings/account.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
