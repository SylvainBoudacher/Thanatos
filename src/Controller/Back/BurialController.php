<?php

namespace App\Controller\Back;

use App\Entity\Burial;
use App\Form\BurialType;
use App\Repository\BurialRepository;
use App\Security\Voter\GeneralVoter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/services/sépultures")]
#[IsGranted("ROLE_COMPANY")]
class BurialController extends AbstractController
{
    #[Route('/', name: 'view_burials')]
    public function view_burial(Request $request, BurialRepository $burialRep): Response
    {
        return $this->render("back/company/services/burials/index.html.twig", [
            "burials" => $burialRep->findBy([
                'deletedAt' => null,
            ], [
                'name' => 'ASC'
            ])
        ]);
    }

    #[Route('/créer', name: 'create_burial')]
    public function create_burial(Request $request, EntityManagerInterface $em): Response
    {
        $burial = new Burial();
        $form = $this->createForm(BurialType::class, $burial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($burial);
            $em->flush();
            return $this->redirectToRoute("view_burials");
        }

        return $this->render("back/company/services/burials/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/modifier/{id}', name: 'modify_burial')]
    public function modify_burial(
        Request                $request,
        EntityManagerInterface $em,
        Burial                 $burial,
        BurialRepository       $burialRep
    ): Response
    {

        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $burial);

        $form = $this->createForm(BurialType::class, $burial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($burial);
            $em->flush();
            return $this->redirectToRoute("view_burials");
        }

        return $this->render("back/company/services/burials/modify.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete_burial')]
    public function delete_burial(EntityManagerInterface $em, Burial $burial, BurialRepository $burialRep): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $burial);

        if (!empty($burial->getModels()->toArray())) throw $this->createAccessDeniedException();

        $burial->setDeletedAt(new DateTime());
        $em->flush();

        return $this->redirectToRoute("view_burials");
    }

}