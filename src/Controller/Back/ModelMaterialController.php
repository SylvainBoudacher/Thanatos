<?php

namespace App\Controller\Back;

use App\Entity\CompanyMaterial;
use App\Entity\Material;
use App\Entity\Model;
use App\Entity\ModelMaterial;
use App\Entity\Preparation;
use App\Repository\CompanyMaterialRepository;
use App\Repository\MaterialRepository;
use App\Repository\ModelMaterialRepository;
use App\Repository\ModelRepository;
use App\Security\Voter\ModelMaterialVoter;
use App\Service\GetterService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
        if ($company == null) return $this->redirectToRoute("home_company");
        // TODO : prévoir le cas ou la company est pas celle de l'user, un servive à utiliser partout

        $models = $modelRep->findBy(["company" => $company, 'deletedAt' => null]);
        return $this->render("back/company/services/model_materials/index.html.twig", [
            "models" => $models
        ]);
    }

    #[Route('/manage/{id}', name: 'manage_model_materials')]
    public function manage_model_materials(Model $model, EntityManagerInterface $em, GetterService $getterService, MaterialRepository $materialRepository, CompanyMaterialRepository $companyMaterialRep, ModelMaterialRepository $modelMaterialRep, Request $request): Response
    {

        $this->denyAccessUnlessGranted(ModelMaterialVoter::EDIT, $model);

        $company = $getterService->getCompanyOfUser();
        $companyMaterials = $companyMaterialRep->findBy(["company" => $company, 'deletedAt' => null]);

        // All exposed materials which are ready to be applied to a model
        $exposedCompanyMaterialsByTheCompany = $companyMaterials;

        // Materials already applied to a model
        $modelMaterialsAvailableForTheModel = $model->getModelMaterials()->toArray();

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
                'label' => false,

            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isAlreadyUsed = false;
            $data = $form->getData();
            $companyMaterialsKeeped = $data["defaultCompanyMaterials"];

            foreach ($companyMaterialsKeeped as $companyMaterialKept) {
                // Make sure the return data are CompanyMaterial entity
                if (!($companyMaterialKept instanceof CompanyMaterial)) throw $this->createAccessDeniedException();
            }

            // get materials ids that the company send from thr form
            $companyMaterialsKeepedId = array_map(function (CompanyMaterial $m) {
                return $m->getMaterial()->getId();
            }, $companyMaterialsKeeped);

            // get materials ids that the company have in resources
            $exposedCompanyMaterialsByTheCompanyId = array_map(function (CompanyMaterial $m) {
                return $m->getMaterial()->getId();
            }, $exposedCompanyMaterialsByTheCompany);

            foreach ($companyMaterialsKeeped as $companyMaterialKept) {
                if (!in_array($companyMaterialKept->getMaterial()->getId(), $exposedCompanyMaterialsByTheCompanyId)) {
                    $this->addFlash('error', 'Un soucis avec votre formulaire est apparu');
                    return $this->redirectToRoute('view_model_materials');
                }
            }

            $currentModelMaterials = $model->getModelMaterials()->toArray();
            $currentModelMaterialsId = array_map(fn($mm) => $mm->getMaterial()->getId(), $currentModelMaterials);
            $materialsToAdd = array_diff($companyMaterialsKeepedId, $currentModelMaterialsId);
            $materialsToRemove = array_diff($currentModelMaterialsId, $companyMaterialsKeepedId);

            // Add new materials relation
            foreach ($materialsToAdd as $materialToAdd) {
                $modelMaterial = new ModelMaterial();
                $modelMaterial->setModel($model);
                $modelMaterial->setMaterial($materialRepository->find($materialToAdd));
                $em->persist($modelMaterial);
            }

            // Remove current materials relation
            foreach ($materialsToRemove as $materialToRemove) {
                $modelMaterial = $modelMaterialRep->findOneBy(['model' => $model, 'material' => $materialToRemove]);
                if ($modelMaterial != null) {
                    $isUsed = $em->getRepository(Preparation::class)->findOneBy(['modelMaterial' => $modelMaterial]);

                    if ($isUsed == null) {
                        $em->remove($modelMaterial);
                    } else {
                        $isAlreadyUsed = true;
                    }
                }
            }

            $em->flush();

            if ($isAlreadyUsed) $this->addFlash('error', "Certains matériaux n'ont pas pu être retiré car ils sont actuellement utilisé dans une commande.");
            $this->addFlash('success', "Les changements ont bien été effectué.");
        }

        return $this->render("back/company/services/model_materials/manage.html.twig", [
            "model" => $model,
            "form" => $form->createView()
        ]);
    }
}