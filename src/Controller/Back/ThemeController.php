<?php

namespace App\Controller\Back;

use App\Entity\Theme;
use App\Form\RegistrationFormType;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[IsGranted("ROLE_ADMIN")]
#[Route("/admin/themes")]
class ThemeController extends AbstractController
{
    #[Route('/', name: 'home_theme')]
    public function index(ThemeRepository $themeRep): Response
    {
        $themes = $themeRep->findAll();
//        dd($themes);

        return $this->render('back/admin/themes/index.html.twig', [
            "themes" => $themes
        ]);
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

    #[Route('/delete/{id}', name: 'delete_theme')]
    public function delete(EntityManagerInterface $em, ThemeRepository $themeRep, int $id): Response
    {
        $theme = $themeRep->find($id);
        $em->remove($theme);
        $em->flush();

        return $this->redirectToRoute("home_theme");
    }

    #[Route('/modify/{id}', name: 'modify_theme')]
    public function modify(EntityManagerInterface $em, Request $request, ThemeRepository $themeRep, int $id): Response
    {
        $theme = $themeRep->find($id);
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($theme);
            $em->flush();
            $this->redirectToRoute("home_theme");
        }

        return $this->render("back/admin/themes/create.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
