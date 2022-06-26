<?php

namespace App\Controller\Back;

use App\Repository\UserRepository;
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

    #[Route('/users/edit/{id}', name: 'admin_user_edit')]
    public function edit(UserRepository $userRepository, $id): Response
    {
        $user = $userRepository->find($id);
        $userRole = $user->getRoles();


        return $this->render('back/admin/users/edit.html.twig', [
            'user' => $user,
            'userRole' => $userRole,
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