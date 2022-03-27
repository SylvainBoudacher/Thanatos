<?php

namespace App\Controller\Back;

use App\Entity\Painting;
use App\Form\PaintingType;
use App\Repository\PaintingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/company/services/paintings")]
#[IsGranted("ROLE_COMPANY")]
class PaintingController extends AbstractController
{
    #[Route('/', name: 'view_paintings')]
    public function view_paintings(PaintingRepository $paintingRep): Response
    {
        return $this->render("back/company/services/paintings/index.html.twig", [
            "paintings" => $paintingRep->findAll(),
        ]);
    }

    #[Route('/details/{id}', name: 'details_painting')]
    public function details_painting(PaintingRepository $paintingRep, int $id): Response
    {
        return $this->render("back/company/services/paintings/details.html.twig", [
            "painting" => $paintingRep->find($id)
        ]);
    }

    #[Route('/create', name: 'create_painting')]
    public function create_painting(Request $request, EntityManagerInterface $em, PaintingRepository $paintingRep): Response
    {
        $painting = new Painting();
        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($painting);
            $em->flush();
            return $this->redirectToRoute("view_paintings");
        }

        return $this->render("back/company/services/paintings/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/modify/{id}', name: 'modify_painting')]
    public function modify_paintings(Request $request, EntityManagerInterface $em, PaintingRepository $paintingRep, int $id): Response
    {
        $painting = $paintingRep->find($id);
        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($painting);
            $em->flush();
            return $this->redirectToRoute("view_paintings");
        }

        return $this->render("back/company/services/paintings/modify.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_painting')]
    public function delete_painting(EntityManagerInterface $em, PaintingRepository $paintingRep, int $id): Response
    {
        $painting = $paintingRep->find($id);
        $media = $painting->getMedia();
        $em->remove($media);
        $em->remove($painting);
        $em->flush();

        // TODO : ATTENTION : Checkez que aucun PAINTING n'est utilisé par un company avant de supprimer

        return $this->redirectToRoute("view_paintings");
    }

}