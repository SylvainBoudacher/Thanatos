<?php

namespace App\Controller\Front\user;

use App\Entity\User;
use App\Form\UserAccountUpdateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function account(Request $request): Response
    {

        $user = new User();
        $form = $this->createForm(UserAccountUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

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
