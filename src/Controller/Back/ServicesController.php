<?php

namespace App\Controller\Back;

use App\Entity\Burial;
use App\Entity\Media;
use App\Entity\Model;
use App\Entity\ModelMedia;
use App\Form\BurialType;
use App\Form\ModelType;
use App\Repository\BurialRepository;
use App\Repository\CompanyRepository;
use App\Repository\ModelMediaRepository;
use App\Repository\ModelRepository;
use App\Repository\UserRepository;
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
    public function view_models(ModelRepository $modelRep): Response
    {
        return $this->render("back/company/services/models/index.html.twig", [
            "models" => $modelRep->findAll(),
        ]);
    }

    #[Route('/models/details/{id}', name: 'details_model')]
    public function details_model(ModelRepository $modelRep, int $id): Response
    {
        $model = $modelRep->find($id);
        return $this->render("back/company/services/models/details.html.twig", [
            "model" => $model,
        ]);
    }

    #[Route('/models/create', name: 'create_model')]
    public function create_model(Request $request, EntityManagerInterface $em, CompanyRepository $companyRep, UserRepository $userRep): Response
    {
        $company = $companyRep->find($userRep->find($this->getUser())->getCompany()->getId());
        $model = new Model();
        $form = $this->createForm(ModelType::class, $model);
        $form->remove("company");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model->setCompany($company);
            $images = $form->get("images")->getData();

            foreach ($images as $image) {
                $modelMedia = new ModelMedia();

                $media = new Media();
                $media->setImageFile($image);

                $modelMedia->setMedia($media);
                $modelMedia->setModel($model);

                $em->persist($modelMedia);
            }

            $em->persist($model);
            $em->flush();
            return $this->redirectToRoute("view_models");

        }

        return $this->render("back/company/services/models/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/models/modify/{id}', name: 'modify_model')]
    public function modify_model(Request $request, EntityManagerInterface $em, int $id, ModelRepository $modelRep, ModelMediaRepository $modelMediaRep): Response
    {
        $model = $modelRep->find($id);
        $form = $this->createForm(ModelType::class, $model);
        $form->remove("company");
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $modelMedias = $modelMediaRep->findBy(["model" => $model]);

            foreach ($modelMedias as $modelMedia) {
                $media = $modelMedia->getMedia();
                $em->remove($media);
                $em->remove($modelMedia);
            }

            $images = $form->get("images")->getData();

            foreach ($images as $image) {
                $modelMedia = new ModelMedia();

                $media = new Media();
                $media->setImageFile($image);

                $modelMedia->setMedia($media);
                $modelMedia->setModel($model);

                $em->persist($modelMedia);
            }

            $em->persist($model);
            $em->flush();
            return $this->redirectToRoute("view_models");

        }

        return $this->render("back/company/services/models/modify.html.twig", [
            "form" => $form->createView(),
            "model" => $model
        ]);
    }

    #[Route('/models/delete/{id}', name: 'delete_model')]
    public function delete_model(EntityManagerInterface $em, int $id, ModelRepository $modelRep, ModelMediaRepository $modelMediaRep): Response
    {
        $model = $modelRep->find($id);
        $modelMedias = $modelMediaRep->findBy(["model" => $model]);

        foreach ($modelMedias as $modelMedia) {
            $media = $modelMedia->getMedia();
            $em->remove($media);
            $em->remove($modelMedia);
        }

        $em->remove($model);
        $em->flush();

        return $this->redirectToRoute("view_models");
    }


}
