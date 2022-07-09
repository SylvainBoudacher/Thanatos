<?php

namespace App\Controller\Back;

use App\Entity\CompanyMaterial;
use App\Repository\CompanyMaterialRepository;
use App\Repository\ModelRepository;
use App\Service\GetterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/company/services/modelMaterials")]
#[IsGranted("ROLE_COMPANY")]
class ModelMaterialController extends AbstractController
{
    #[Route('/', name: 'view_model_materials')]
    public function view_model_materials(GetterService $getterService, ModelRepository $modelRep): Response
    {
        $company = $getterService->getCompanyOfUser();
        if ($company == null) $this->redirectToRoute("home_company");
        // TODO : prévoir le cas ou la company est pas celle de l'user

        $models = $modelRep->findBy(["company" => $company]);
        return $this->render("back/company/services/model_materials/index.html.twig", [
            "models" => $models
        ]);
    }

    #[Route('/manage/{id}', name: 'manage_model_materials')]
    public function manage_model_materials(int $id, GetterService $getterService, ModelRepository $modelRep, CompanyMaterialRepository $companyMaterialRep): Response
    {
        $model = $modelRep->findOneBy(["id" => $id]);
        $company = $getterService->getCompanyOfUser();
        $companyMaterials = $companyMaterialRep->findBy(["company" => $company]);
        dd($companyMaterials);

        $default = [
            'defaultCompanyMaterials' => $companyMaterials
        ];

        // TODO : plus meme verifications que au dessus

        $form = $this->createFormBuilder($default)
            ->add('defaultCompanyMaterials', EntityType::class, [
                'class' => CompanyMaterial::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->getForm();

        return $this->render("back/company/services/model_materials/manage.html.twig", [
            "model" => $modelRep,
            "form" => $form->createView()
        ]);
    }




}