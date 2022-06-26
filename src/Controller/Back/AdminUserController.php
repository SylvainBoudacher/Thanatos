<?php

namespace App\Controller\Back;

use App\Form\UserAccountUpdateFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted("ROLE_ADMIN")]
#[Route("/admin")]

class AdminUserController extends AbstractController
{
    #[Route('/users/list', name: 'admin_users_list')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('back/admin/users/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/edit/{id}', name: 'admin_user_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request , UserRepository $userRepository , int $id): Response
    {
        $user = $userRepository->find($id);
        $form = $this->createForm(UserAccountUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('session')->migrate();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                'Vos données ont bien été changer'
            );

            return $this->redirectToRoute('admin_user_edit', ['id' => $id]);
        }

        return $this->render('back/admin/users/edit.html.twig', [
            'AdminUserUpdateForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/users/delete/{id}', name: 'admin_user_delete')]
    public function delete(UserRepository $userRepository, $id): Response
    {
        $user = $userRepository->find($id);

        return $this->render('back/admin/users/delete.html.twig', [
            'user' => $user,
        ]);
    }



}