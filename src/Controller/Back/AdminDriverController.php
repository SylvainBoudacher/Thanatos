<?php

namespace App\Controller\Back;

use App\Repository\CompanyRepository;
use App\Repository\DriverOrderRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]
#[Route("/admin")]
class AdminDriverController extends AbstractController
{
    #[Route('/utilisateurs/conducteurs', name: 'admin_driver_list')]
    public function driversList(CompanyRepository $companyRepository, UserRepository $userRepository): Response
    {
        $companies = $companyRepository->findByType('DRIVER');

        return $this->render('back/admin/driver/list.html.twig', [
            'companies' => $companies,

        ]);
    }

}