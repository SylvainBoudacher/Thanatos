<?php

namespace App\Controller\Back;

use App\Entity\CompanyPainting;
use App\Entity\Painting;
use App\Form\PaintingType;
use App\Repository\CompanyPaintingRepository;
use App\Repository\PaintingRepository;
use App\Repository\UserRepository;
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
    public function view_paintings(PaintingRepository $paintingRep, UserRepository $userRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        $paintingsSuscribedByCompany = $paintingRep->getAllByCompany($company);

        return $this->render("back/company/services/paintings/index.html.twig", [
            "paintings" => $paintingRep->findAll(),
            "paintingsSuscribedByCompany" => $paintingsSuscribedByCompany
        ]);
    }


    /* THANATOS DATABASE RELATED */


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

    /* EXPOSED TO THE CLIENTS */

    #[Route('/switch/{id}', name: 'switch_painting')]
    public function switch_painting(int $id, EntityManagerInterface $em, UserRepository $userRep, CompanyPaintingRepository $companyPaintingRep, PaintingRepository $paintingRep) : Response {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();
        $painting = $paintingRep->find($id);

        // TODO : Meilleur vérification plus tard (genre theme désactivé par thanatos)
        if (empty($company)) {
            $this->addFlash("error", "Une erreur est survenue. Veuillez réessayez ultérieurement");
            return $this->redirectToRoute("view_paintings");
        }

        $companyPainting = $companyPaintingRep->getOneByCompanyAndPainting($company, $painting);

        if ($companyPainting) {
            $em->remove($companyPainting);
            $em->flush();
            $this->addFlash("success", "La couleur ".$painting->getName()." ne sera plus disponible pour les clients");
            return $this->redirectToRoute("view_paintings");
        }

        $companyPainting = new CompanyPainting();
        $companyPainting->setCompany($company);
        $companyPainting->setPainting($painting);

        $em->persist($companyPainting);
        $em->flush();

        $this->addFlash("success", "La couleur ".$painting->getName()." est désormais disponibles pour les clients");
        return $this->redirectToRoute("view_paintings");

    }
}