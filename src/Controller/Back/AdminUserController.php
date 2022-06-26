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



}