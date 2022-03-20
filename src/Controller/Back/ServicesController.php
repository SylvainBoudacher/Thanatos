<?php

namespace App\Controller\Back;

use App\Entity\Burial;
use App\Entity\Model;
use App\Form\BurialType;
use App\Form\ModelType;
use App\Repository\BurialRepository;
use App\Repository\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/company/services")] // TODO : Peut-être faire /company/{nomDeLaCompagnie}/services dans le futur
#[IsGranted("ROLE_COMPANY")]
class ServicesController extends AbstractController
{
    #[Route('/', name: 'home_services')]
    public function index(): Response
    {
        return $this->render('back/company/services/index.html.twig');
    }

    /* BURIALS */

    #[Route('/burials', name: 'view_burials')]
    public function view_burial(Request $request, BurialRepository $burialRep): Response
    {
        return $this->render("back/company/services/burials/index.html.twig", [
            "burials" => $burialRep->findAll()
        ]);
    }

    #[Route('/burials/create', name: 'create_burial')]
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

    #[Route('/burials/modify/{id}', name: 'modify_burial')]
    public function modify_burial(Request $request, EntityManagerInterface $em, int $id, BurialRepository $burialRep): Response
    {
        $burial = $burialRep->find($id);
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

    #[Route('/burials/delete/{id}', name: 'delete_burial')]
    public function delete_burial(EntityManagerInterface $em, int $id, BurialRepository $burialRep): Response
    {
        $burial = $burialRep->find($id);
        $em->remove($burial);
        $em->flush();
        return $this->redirectToRoute("view_burials");

    }

    /* MODELS */

    #[Route('/models', name: 'view_models')]
    public function view_models(Request $request, ModelRepository $modelRep): Response
    {
        return $this->render("back/company/services/models/index.html.twig", [
            "models" => $modelRep->findAll()
        ]);
    }

    #[Route('/models/create', name: 'create_model')]
    public function create_model(Request $request, EntityManagerInterface $em): Response
    {
        $model = new Model();
        $form = $this->createForm(ModelType::class, $model);
        $form->remove("company");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($model);
            $em->flush();
            return $this->redirectToRoute("view_models");
        }

        return $this->render("back/company/services/models/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/models/modify/{id}', name: 'modify_model')]
    public function modify_model(Request $request, EntityManagerInterface $em, int $id, ModelRepository $modelRep): Response
    {
        $model = $modelRep->find($id);
        $form = $this->createForm(ModelType::class, $model);
        $form->remove("company");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($model);
            $em->flush();
            return $this->redirectToRoute("view_models");
        }

        return $this->render("back/company/services/models/modify.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/models/delete/{id}', name: 'delete_model')]
    public function delete_model(EntityManagerInterface $em, int $id, ModelRepository $modelRep): Response
    {
        $model = $modelRep->find($id);
        $em->remove($model);
        $em->flush();
        return $this->redirectToRoute("view_models");

    }


}
