<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserAccountUpdateFormType;
use App\Repository\UserRepository;
use App\Security\Voter\GeneralVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted("ROLE_ADMIN")]
#[Route("/admin")]
class AdminUserController extends AbstractController
{

    #[Route('/utilisateurs/list', name: 'admin_users_list')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('back/admin/users/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/utilisateurs/modifier/{id}', name: 'admin_user_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, UserRepository $userRepository, User $user, EntityManagerInterface $em): Response
    {

        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $user);

        $form = $this->createForm(UserAccountUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                "Les données de l'utilisateur " . $user->getFirstname() . " " . $user->getLastname() . " ont bien été changer"
            );

            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        return $this->render('back/admin/users/edit.html.twig', [
            'AdminUserUpdateForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    /* THANATOS DATABASE RELATED */

    /*  #[Route('/utilisateurs/supprimer/{id}', name: 'admin_user_delete')]
      public function delete(UserRepository $userRepository, $id): Response
      {
          $user = $userRepository->find($id);

          return $this->render('back/admin/users/delete.html.twig', [
              'user' => $user,
          ]);
      }*/

}