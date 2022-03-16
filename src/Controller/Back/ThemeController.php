<?php

namespace App\Controller\Back;

use App\Entity\Theme;
use App\Form\RegistrationFormType;
use App\Form\ThemeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]
#[Route("/admin/themes")]
class ThemeController extends AbstractController
{
    #[Route('/', name: 'home_theme')]
    public function index(): Response
    {
        return $this->render('back/admin/themes/index.html.twig',);
    }

    #[Route('/create', name: 'create_theme')]
    public function create(Request $request): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($theme);
            $entityManager->flush();

        }

        return $this->render('back/admin/themes/create.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
