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
        $user = $this->getUser();
        $form = $this->createForm(UserAccountUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                'Vos données ont bien été changer'
            );
            return $this->redirectToRoute('app_settings_account');
        }
        return $this->render('front/user/settings/account.html.twig', [
            'userAccountUpdateForm' => $form->createView(),
        ]);
    }
    /**  ********************* */

    #[Route('/wallet', name: 'app_settings_wallet')]
    public function wallet(): Response
    {
        return $this->render('front/user/settings/wallet.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**  ********************* */

    #[Route('/password', name: 'app_settings_password')]
    public function password(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserAccountUpdateFormType::class, $user);
        $form->handleRequest($request);
        
        return $this->render('front/user/settings/password.html.twig', [
            'ChangeUserPasswordForm' => $form->createView(),
        ]);
    }
}
