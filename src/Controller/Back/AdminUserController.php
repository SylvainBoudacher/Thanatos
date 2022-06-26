<?php

namespace App\Controller\Back;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]
#[Route("/admin")]

class AdminUserController extends AbstractController
{
    #[Route('/users/list', name: 'admin_users_list')]
    public function users(): Response
    {
        return $this->render('back/admin/usersList.html.twig',);
    }

}