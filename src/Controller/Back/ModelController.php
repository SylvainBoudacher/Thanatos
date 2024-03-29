<?php

namespace App\Controller\Back;

use App\Entity\Media;
use App\Entity\Model;
use App\Entity\ModelMedia;
use App\Form\ModelType;
use App\Repository\CompanyRepository;
use App\Repository\ModelMediaRepository;
use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use App\Security\Voter\GeneralVoter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\AssignOp\Mod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/services/modèles")]
#[IsGranted("ROLE_COMPANY")]
class ModelController extends AbstractController
{
    #[Route('/', name: 'view_models')]
    public function view_models(ModelRepository $modelRep, CompanyRepository $companyRep, UserRepository $userRep): Response
    {
        $company = $companyRep->find($userRep->find($this->getUser())->getCompany()->getId());
        $models = $modelRep->findBy(["company" => $company, "deletedAt" => null], ['name' => 'ASC']);

        return $this->render("back/company/services/models/index.html.twig", [
            "models" => $models
        ]);
    }

    #[Route('/details/{id}', name: 'details_model')]
    public function details_model(ModelRepository $modelRep, Model $model): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $model);
        $this->denyAccessUnlessGranted(GeneralVoter::OWNED_COMPANY, $model);

        return $this->render("back/company/services/models/details.html.twig", [
            "model" => $model,
        ]);
    }

    #[Route('/créer', name: 'create_model')]
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

    #[Route('/modifier/{id}', name: 'modify_model')]
    public function modify_model(Request $request, EntityManagerInterface $em, Model $model, ModelRepository $modelRep, ModelMediaRepository $modelMediaRep): Response
    {

        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $model);
        $this->denyAccessUnlessGranted(GeneralVoter::OWNED_COMPANY, $model);

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

    #[Route('/supprimer/{id}', name: 'delete_model')]
    public function delete_model(EntityManagerInterface $em, Model $model, ModelRepository $modelRep, ModelMediaRepository $modelMediaRep): Response
    {

        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $model);
        $this->denyAccessUnlessGranted(GeneralVoter::OWNED_COMPANY, $model);

        if (!$model->canBeDeleted()) throw $this->createAccessDeniedException();

        $model->setDeletedAt(new DateTime());

        $em->flush();

        return $this->redirectToRoute("view_models");
    }

}