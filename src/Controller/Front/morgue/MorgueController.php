<?php

namespace App\Controller\Front\morgue;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_MORGUE")]
#[Route("/morgue")]
class MorgueController extends AbstractController
{
    /**
     * @IsGranted("ROLE_MORGUE")
    */
    #[Route('/', name: 'morgue_dashboard')]
    public function index(): Response
    {
        return $this->render('front/morgue/index.html.twig', [
            'controller_name' => 'MorgueController',
        ]);
    }
}
