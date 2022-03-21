<?php

namespace App\Controller\Back;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/company")] // TODO : Peut-être faire /company/{nomDeLaCompagnie} dans le futur
#[IsGranted("ROLE_COMPANY")]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'home_company')]
    public function index(): Response
    {
        return $this->render('back/company/index.html.twig');
    }


}
