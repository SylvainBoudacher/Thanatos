<?php

namespace App\Controller\Front\user;


use App\Entity\CreditCard;
use App\Entity\User;
use App\Entity\Media;
use App\Form\NewCreditCardFormType;
use App\Form\UserAccountUpdateFormType;
use App\Repository\CreditCardRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/parametres')]
class UserController extends AbstractController
{
    #[Route('/compte', name: 'app_settings_account')]
    public function account(Request $request, UserRepository $userRep): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $userRep->find($this->getUser());
        $form = $this->createForm(UserAccountUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                'Vos données ont bien été changées'
            );

            return $this->redirectToRoute('app_settings_account');
        }
        return $this->render('front/user/settings/account.html.twig', [
            'userAccountUpdateForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**  ********************* */

    /*  #[Route('/mot-de-passe', name: 'app_settings_password')]
      public function password(Request $request): Response
      {
          $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

          $user = $this->getUser();
          $form = $this->createForm(UserAccountUpdateFormType::class, $user);
          $form->handleRequest($request);

          return $this->render('front/user/settings/password.html.twig', [
              'ChangeUserPasswordForm' => $form->createView(),
          ]);
      }*/
}
