<?php

namespace App\Controller\Back;

use App\Repository\CompanyRepository;
use App\Repository\DriverOrderRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]
#[Route("/admin")]

class AdminDriverController extends AbstractController
{
    #[Route('/users/driver', name: 'admin_driver_list')]
    public function driversList(CompanyRepository $companyRepository , UserRepository $userRepository): Response
    {

        return $this->render('back/admin/driver/list.html.twig', [

        ]);
    }

}