<?php

namespace App\Controller\Back;

use App\Entity\CompanyPainting;
use App\Entity\Painting;
use App\Entity\Preparation;
use App\Form\PaintingType;
use App\Repository\CompanyPaintingRepository;
use App\Repository\PaintingRepository;
use App\Repository\UserRepository;
use App\Security\Voter\GeneralVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/services/peintures")]
#[IsGranted("ROLE_COMPANY")]
class PaintingController extends AbstractController
{
    #[Route('/', name: 'view_paintings')]
    public function view_paintings(PaintingRepository $paintingRep, UserRepository $userRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        $paintings = $paintingRep->findBy([
            'deletedAt' => null,
        ], [
            'name' => 'ASC'
        ]);
        $paintingsSuscribedByCompany = $paintingRep->getAllByCompany($company);

        foreach ($paintings as $painting) {
            $painting->canBeDeleted = $this->canBeDeleted($painting);
            $painting->canBeSwitched = $this->canBeSwitched($painting);

        }

        foreach ($paintingsSuscribedByCompany as $painting) {
            $painting->canBeSwitched = $this->canBeSwitched($painting);
        }


        return $this->render("back/company/services/paintings/index.html.twig", [
            "paintings" => $paintings,
            "paintingsSuscribedByCompany" => $paintingsSuscribedByCompany
        ]);
    }


    /* THANATOS DATABASE RELATED */
    private function canBeDeleted(Painting $painting): bool
    {
        $em = $this->getDoctrine()->getManager();
        $preparations = $em->getRepository(Preparation::class)->findAll();

        if (!empty($preparations)) $preparations = array_map(function ($p) {
            if ($p->getModelMaterial() !== null)
                return $p->getModelMaterial()->getId();
        }, $preparations);

        return empty($painting->getCompanyPaintings()->toArray()) && !in_array($painting->getId(), $preparations);
    }

    private function canBeSwitched(Painting $painting): bool
    {
        $em = $this->getDoctrine()->getManager();
        $preparations = $em->getRepository(Preparation::class)->findAll();

        if (!empty($preparations)) $preparations = array_map(function ($p) {
            if ($p->getModelMaterial() !== null)
                return $p->getModelMaterial()->getId();
        }, $preparations);

        return !in_array($painting->getId(), $preparations);
    }


    #[Route('/details/{id}', name: 'details_painting')]
    public function details_painting(Painting $painting): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $painting);

        return $this->render("back/company/services/paintings/details.html.twig", [
            "painting" => $painting
        ]);
    }

    #[Route('/créer', name: 'create_painting')]
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

    #[Route('/modifier/{id}', name: 'modify_painting')]
    public function modify_paintings(Request $request, EntityManagerInterface $em, PaintingRepository $paintingRep, Painting $painting): Response
    {

        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $painting);

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

    #[Route('/supprimer/{id}', name: 'delete_painting')]
    public function delete_painting(EntityManagerInterface $em, PaintingRepository $paintingRep, Painting $painting): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $painting);
        if (!$this->canBeDeleted($painting)) throw $this->createAccessDeniedException();

        $media = $painting->getMedia();
        $em->remove($media);
        $em->remove($painting);
        $em->flush();

        // TODO : ATTENTION : Checkez que aucun PAINTING n'est utilisé par un company avant de supprimer

        return $this->redirectToRoute("view_paintings");
    }

    /* EXPOSED TO THE CLIENTS */

    #[Route('/switch/{id}', name: 'switch_painting')]
    public function switch_painting(Painting $painting, EntityManagerInterface $em, UserRepository $userRep, CompanyPaintingRepository $companyPaintingRep, PaintingRepository $paintingRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        // TODO : Meilleur vérification plus tard (genre theme désactivé par thanatos)
        if (empty($company)) {
            $this->addFlash("error", "Une erreur est survenue. Veuillez réessayez ultérieurement");
            return $this->redirectToRoute("view_paintings");
        }
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $painting);

        $companyPainting = $companyPaintingRep->getOneByCompanyAndPainting($company, $painting);

        if ($companyPainting) {
            if (!$this->canBeSwitched($painting)) {
                $this->addFlash("error", "La couleur " . $painting->getName() . " ne peut être retiré car il est utilisé dans une commande");
                return $this->redirectToRoute("view_paintings");
            }
            $em->remove($companyPainting);
            $em->flush();
            $this->addFlash("success", "La couleur " . $painting->getName() . " ne sera plus disponible pour les clients");
            return $this->redirectToRoute("view_paintings");
        }

        $companyPainting = new CompanyPainting();
        $companyPainting->setCompany($company);
        $companyPainting->setPainting($painting);

        $em->persist($companyPainting);
        $em->flush();

        $this->addFlash("success", "La couleur " . $painting->getName() . " est désormais disponibles pour les clients");
        return $this->redirectToRoute("view_paintings");

    }
}