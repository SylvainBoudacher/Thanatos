<?php

namespace App\Controller\Back;

use App\Entity\CompanyMaterial;
use App\Entity\Model;
use App\Repository\CompanyMaterialRepository;
use App\Repository\ModelMaterialRepository;
use App\Repository\ModelRepository;
use App\Service\GetterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/services/modele-materiel")]
#[IsGranted("ROLE_COMPANY")]
class ModelMaterialController extends AbstractController
{
    #[Route('/', name: 'view_model_materials')]
    public function view_model_materials(GetterService $getterService, ModelRepository $modelRep): Response
    {
        $company = $getterService->getCompanyOfUser();
        if ($company == null) $this->redirectToRoute("home_company");
        // TODO : prévoir le cas ou la company est pas celle de l'user, un servive à utiliser partout

        $models = $modelRep->findBy(["company" => $company]);
        return $this->render("back/company/services/model_materials/index.html.twig", [
            "models" => $models
        ]);
    }

    #[Route('/manage/{id}', name: 'manage_model_materials')]
    public function manage_model_materials(Model $model, GetterService $getterService, ModelRepository $modelRep, CompanyMaterialRepository $companyMaterialRep): Response
    {
        $company = $getterService->getCompanyOfUser();
        if ($model->getCompany()->getId() !== $company?->getId()) dd('page error');

        $companyMaterials = $companyMaterialRep->findBy(["company" => $company]);
        dd($companyMaterials);

        // Materials exposé par la company et peut être appliqué à un model
        $exposedCompanyMaterialsByTheCompany = $companyMaterialRep->findBy(["company" => $company]);

        // TODO : get modelmaterial de company modelMaterial.model.company =
        $truc = $modelMaterialRep->getByCompany($company);
        dd($truc);

        // TODO : mettre dans le form toutes les CompanyMaterials, et coché les materials qui sont dans MODEL_MATERIAL


        $default = [
            'defaultCompanyMaterials' => $exposedCompanyMaterialsByTheCompany
        ];


        $form = $this->createFormBuilder($default)
            ->add('truc', EntityType::class, [
                'class' => CompanyMaterial::class,
                'choices' => $exposedCompanyMaterialsByTheCompany,
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