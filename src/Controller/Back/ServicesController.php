<?php

namespace App\Controller\Back;

use App\Entity\Burial;
use App\Entity\Extra;
use App\Entity\Material;
use App\Entity\Media;
use App\Entity\Model;
use App\Entity\ModelMedia;
use App\Entity\Painting;
use App\Form\BurialType;
use App\Form\ExtraType;
use App\Form\MaterialType;
use App\Form\ModelType;
use App\Form\PaintingType;
use App\Repository\BurialRepository;
use App\Repository\CompanyRepository;
use App\Repository\ExtraRepository;
use App\Repository\MaterialRepository;
use App\Repository\ModelMediaRepository;
use App\Repository\ModelRepository;
use App\Repository\PaintingRepository;
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


    /* MATERIALS */


    #[Route('/materials', name: 'view_materials')]
    public function view_materials(MaterialRepository $materialRep): Response
    {
        return $this->render("back/company/services/materials/index.html.twig", [
            "materials" => $materialRep->findAll(),
        ]);
    }

    #[Route('/materials/details/{id}', name: 'details_material')]
    public function details_materials(MaterialRepository $materialRep, int $id): Response
    {
        return $this->render("back/company/services/materials/details.html.twig", [
            "material" => $materialRep->find($id)
        ]);
    }

    #[Route('/materials/create', name: 'create_material')]
    public function create_materials(Request $request, EntityManagerInterface $em, MaterialRepository $materialRep): Response
    {
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($material);
            $em->flush();
            return $this->redirectToRoute("view_materials");
        }

        return $this->render("back/company/services/materials/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/materials/modify/{id}', name: 'modify_material')]
    public function modify_materials(Request $request, EntityManagerInterface $em, MaterialRepository $materialRep, int $id): Response
    {
        $material = $materialRep->find($id);
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($material);
            $em->flush();
            return $this->redirectToRoute("view_materials");
        }

        return $this->render("back/company/services/materials/modify.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/materials/delete/{id}', name: 'delete_material')]
    public function delete_materials(EntityManagerInterface $em, MaterialRepository $materialRep, int $id): Response
    {
        $material = $materialRep->find($id);
        $media = $material->getMedia();
        $em->remove($media);
        $em->remove($material);
        $em->flush();

        // TODO : ATTENTION : Checkez que aucuns materials n'est utilise par un company avant de supprimer

        return $this->redirectToRoute("view_materials");
    }


    /* PEINTURE */


    #[Route('/paintings', name: 'view_paintings')]
    public function view_paintings(PaintingRepository $paintingRep): Response
    {
        return $this->render("back/company/services/paintings/index.html.twig", [
            "paintings" => $paintingRep->findAll(),
        ]);
    }

    #[Route('/paintings/details/{id}', name: 'details_painting')]
    public function details_painting(PaintingRepository $paintingRep, int $id): Response
    {
        return $this->render("back/company/services/paintings/details.html.twig", [
            "painting" => $paintingRep->find($id)
        ]);
    }

    #[Route('/paintings/create', name: 'create_painting')]
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

    #[Route('/paintings/modify/{id}', name: 'modify_painting')]
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

    #[Route('/paintings/delete/{id}', name: 'delete_painting')]
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


    /* EXTRAS */

    #[Route('/extras', name: 'view_extras')]
    public function view_extras(ExtraRepository $extraRep): Response
    {
        return $this->render("back/company/services/extras/index.html.twig", [
            "extras" => $extraRep->findAll(),
        ]);
    }

    #[Route('/extras/details/{id}', name: 'details_extra')]
    public function details_extra(ExtraRepository $extraRep, int $id): Response
    {
        return $this->render("back/company/services/extras/details.html.twig", [
            "extra" => $extraRep->find($id)
        ]);
    }

    #[Route('/extras/create', name: 'create_extra')]
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

    #[Route('/extras/modify/{id}', name: 'modify_extra')]
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

    #[Route('/extras/delete/{id}', name: 'delete_extra')]
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
