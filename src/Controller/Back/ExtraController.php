<?php

namespace App\Controller\Back;

use App\Entity\Extra;
use App\Form\ExtraType;
use App\Repository\ExtraRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/company/services/extras")]
#[IsGranted("ROLE_COMPANY")]
class ExtraController extends AbstractController
{
    #[Route('/', name: 'view_extras')]
    public function view_extras(ExtraRepository $extraRep): Response
    {
        return $this->render("back/company/services/extras/index.html.twig", [
            "extras" => $extraRep->findAll(),
        ]);
    }

    #[Route('/details/{id}', name: 'details_extra')]
    public function details_extra(ExtraRepository $extraRep, int $id): Response
    {
        return $this->render("back/company/services/extras/details.html.twig", [
            "extra" => $extraRep->find($id)
        ]);
    }

    #[Route('/create', name: 'create_extra')]
    public function create_extra(Request $request, EntityManagerInterface $em): Response
    {
        $extra = new Extra();
        $form = $this->createForm(ExtraType::class, $extra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($extra);
            $em->flush();
            return $this->redirectToRoute("view_extras");
        }

        return $this->render("back/company/services/extras/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/modify/{id}', name: 'modify_extra')]
    public function modify_extras(Request $request, EntityManagerInterface $em, ExtraRepository $extraRep, int $id): Response
    {
        $extra = $extraRep->find($id);
        $form = $this->createForm(ExtraType::class, $extra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($extra);
            $em->flush();
            return $this->redirectToRoute("view_extras");
        }

        return $this->render("back/company/services/extras/modify.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_extra')]
    public function delete_extra(EntityManagerInterface $em, ExtraRepository $extraRep, int $id): Response
    {
        $extra = $extraRep->find($id);
        $media = $extra->getMedia();
        $em->remove($media);
        $em->remove($extra);
        $em->flush();

        // TODO : ATTENTION : Checkez que aucun Extras n'est utilisé par un company avant de supprimer

        return $this->redirectToRoute("view_extras");
    }

}