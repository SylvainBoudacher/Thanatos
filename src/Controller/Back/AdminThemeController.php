<?php

namespace App\Controller\Back;

use App\Entity\Media;
use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use App\Security\Voter\GeneralVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]
#[Route("/admin/themes")]
class AdminThemeController extends AbstractController
{
    #[Route('/', name: 'home_theme')]
    public function index(ThemeRepository $themeRep): Response
    {
        $themes = $themeRep->findBy(['deletedAt' => null]);

        foreach ($themes as $theme) {
            $theme->canBeDeleted = $this->canDelete($theme);
        }

        return $this->render('back/admin/themes/index.html.twig', [
            "themes" => $themes
        ]);
    }

    private function canDelete(Theme $entity): bool
    {

        if (!empty($entity->getCompanyThemes()->toArray())) return false;
        if (!empty($entity->getPreparations()->toArray())) return false;

        return true;
    }

    #[Route('/créer', name: 'create_theme')]
    public function create(Request $request): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute("home_theme");
        }

        return $this->render('back/admin/themes/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete_theme')]
    public function delete(EntityManagerInterface $em, ThemeRepository $themeRep, Theme $theme): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $theme);
        $this->denyAccessUnlessGranted(GeneralVoter::PREPARATION_INCLUDED, $theme);
        $this->denyAccessUnlessGranted(GeneralVoter::DELETE, $theme);

        $media = $theme->getMedia();

        if ($media instanceof Media) $em->remove($media);
        $em->remove($theme);
        $em->flush();

        // TODO : Warning, later if there are company that uses a specific theme while an order is not finished


        return $this->redirectToRoute("home_theme");
    }

    #[Route('/modifier/{id}', name: 'modify_theme')]
    public function modify(EntityManagerInterface $em, Request $request, ThemeRepository $themeRep, Theme $theme): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $theme);

        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($theme);
            $em->flush();
            return $this->redirectToRoute("home_theme");
        }

        return $this->render("back/admin/themes/modify.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
