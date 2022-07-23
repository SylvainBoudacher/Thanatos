<?php

namespace App\Controller\Back;

use App\Entity\CompanyMaterial;
use App\Entity\Model;
use App\Repository\CompanyMaterialRepository;
use App\Repository\ModelMaterialRepository;
use App\Repository\ModelRepository;
use App\Service\GetterService;
use Symfony\Component\HttpFoundation\Request;
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

        // All exposed materials which are ready to be applied to a model
        $exposedCompanyMaterialsByTheCompany = $companyMaterialRep->findBy(["company" => $company]);

        // Materials already applied to a model
        $modelMaterialsAvailableForTheModel = $modelMaterialRep->getByCompanyAndModel($company, $model);

        // Array of CompanyMaterial already applied to a model. Used as default values for checkboxes below
        $markChecked = [];
        foreach ($exposedCompanyMaterialsByTheCompany as $companyMaterial) {
            $materialRef = $companyMaterial->getMaterial();
            foreach ($modelMaterialsAvailableForTheModel as $modelMaterial) {
                $material = $modelMaterial->getMaterial();
                if ($materialRef == $material) {
                    $markChecked[] = $companyMaterial;
                    break;
                }
            }
        }

        $default = [
            'defaultCompanyMaterials' => $markChecked
        ];

        $form = $this->createFormBuilder($default)
            ->add('defaultCompanyMaterials', EntityType::class, [
                'class' => CompanyMaterial::class,
                'choices' => $exposedCompanyMaterialsByTheCompany,
                'multiple' => true,
                'expanded' => true,
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $CompanyMaterialsKeeped = $data["defaultCompanyMaterials"];

            foreach ($exposedCompanyMaterialsByTheCompany as $companyMaterial) {
                $material = $companyMaterial->getMaterial();

                foreach ($CompanyMaterialsKeeped as $CompanyMaterialKept) {
                    $materialKept = $CompanyMaterialKept->getMaterial();
                    if ($material->getId() === $materialKept->getId()) {

                    }
                }
            }
        }

        return $this->render("back/company/services/model_materials/manage.html.twig", [
            "model" => $modelRep,
            "form" => $form->createView()
        ]);
    }
}