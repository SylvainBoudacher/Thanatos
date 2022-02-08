<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    #[Route('/', name: 'landingPage')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'StaticController',
        ]);
    }
}
