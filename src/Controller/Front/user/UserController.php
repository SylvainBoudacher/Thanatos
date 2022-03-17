<?php

namespace App\Controller\Front\user;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/settings')]
class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     */

    #[Route('/account', name: 'app_settings_account')]
    public function account(): Response
    {
        return $this->render('front/user/settings/account.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/wallet', name: 'app_settings_wallet')]
    public function wallet(): Response
    {
        return $this->render('front/user/settings/wallet.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/password', name: 'app_settings_password')]
    public function password(): Response
    {
        return $this->render('front/user/settings/password.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
