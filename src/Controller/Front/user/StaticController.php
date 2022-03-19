<?php

namespace App\Controller\Front\user;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class StaticController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
    */

    #[Route('/user', name: 'user_dashboard')]
    public function index(): Response
    {
        return $this->render('front/user/index.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }

}