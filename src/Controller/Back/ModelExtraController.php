<?php

namespace App\Controller\Back;

use App\Entity\CompanyExtra;
use App\Entity\Model;
use App\Entity\ModelExtra;
use App\Entity\Preparation;
use App\Repository\CompanyExtraRepository;
use App\Repository\ExtraRepository;
use App\Repository\ModelExtraRepository;
use App\Repository\ModelRepository;
use App\Security\Voter\ModelExtraVoter;
use App\Service\GetterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/services/modele-extra")]
#[IsGranted("ROLE_COMPANY")]
class ModelExtraController extends AbstractController
{
    #[Route('/', name: 'view_model_extras')]
    public function view_model_extras(GetterService $getterService, ModelRepository $modelRep): Response
    {
        $company = $getterService->getCompanyOfUser();
        if ($company == null) return $this->redirectToRoute("home_company");
        // TODO : prévoir le cas ou la company est pas celle de l'user, un servive à utiliser partout

        $models = $modelRep->findBy(["company" => $company, 'deletedAt' => null]);
        return $this->render("back/company/services/model_extras/index.html.twig", [
            "models" => $models
        ]);
    }

    #[Route('/manage/{id}', name: 'manage_model_extras')]
    public function manage_model_extras(Model $model, EntityManagerInterface $em, GetterService $getterService, ExtraRepository $extraRepository, CompanyExtraRepository $companyExtraRepository, ModelExtraRepository $modelExtraRepository, Request $request): Response
    {

        $this->denyAccessUnlessGranted(ModelExtraVoter::EDIT, $model);

        $company = $getterService->getCompanyOfUser();
        $companyExtras = $companyExtraRepository->findBy(["company" => $company, 'deletedAt' => null]);

        // All exposed extras which are ready to be applied to a model
        $exposedCompanyExtrasByTheCompany = $companyExtras;

        // Extras already applied to a model
        $modelExtrasAvailableForTheModel = $model->getModelExtras()->toArray();

        // Array of CompanyExtra already applied to a model. Used as default values for checkboxes below
        $markChecked = [];
        foreach ($exposedCompanyExtrasByTheCompany as $companyExtra) {
            $extraRef = $companyExtra->getExtra();
            foreach ($modelExtrasAvailableForTheModel as $modelExtra) {
                $extra = $modelExtra->getExtra();
                if ($extraRef == $extra) {
                    $markChecked[] = $companyExtra;
                    break;
                }
            }
        }

        $default = [
            'defaultCompanyExtras' => $markChecked
        ];

        $form = $this->createFormBuilder($default)
            ->add('defaultCompanyExtras', EntityType::class, [
                'class' => CompanyExtra::class,
                'choices' => $exposedCompanyExtrasByTheCompany,
                'multiple' => true,
                'expanded' => true,
                'label' => false,

            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $isAlreadyUsed = false;
            $data = $form->getData();
            $companyExtrasKeeped = $data["defaultCompanyExtras"];

            foreach ($companyExtrasKeeped as $companyExtraKept) {
                // Make sure the return data are CompanyExtra entity
                if (!($companyExtraKept instanceof CompanyExtra)) throw $this->createAccessDeniedException();
            }

            // get extras ids that the company send from thr form
            $companyExtrasKeepedId = array_map(function (CompanyExtra $m) {
                return $m->getExtra()->getId();
            }, $companyExtrasKeeped);

            // get extras ids that the company have in resources
            $exposedCompanyExtrasByTheCompanyId = array_map(function (CompanyExtra $m) {
                return $m->getExtra()->getId();
            }, $exposedCompanyExtrasByTheCompany);

            foreach ($companyExtrasKeeped as $companyExtraKept) {
                if (!in_array($companyExtraKept->getExtra()->getId(), $exposedCompanyExtrasByTheCompanyId)) {
                    $this->addFlash('error', 'Un soucis avec votre formulaire est apparu');
                    return $this->redirectToRoute('view_model_extras');
                }
            }

            $currentModelExtras = $model->getModelExtras()->toArray();
            $currentModelExtrasId = array_map(fn($mm) => $mm->getExtra()->getId(), $currentModelExtras);
            $extrasToAdd = array_diff($companyExtrasKeepedId, $currentModelExtrasId);
            $extrasToRemove = array_diff($currentModelExtrasId, $companyExtrasKeepedId);

            // Add new extras relation
            foreach ($extrasToAdd as $extraToAdd) {
                $modelExtra = new ModelExtra();
                $modelExtra->setModel($model);
                $modelExtra->setExtra($extraRepository->find($extraToAdd));
                $em->persist($modelExtra);
            }

            // Remove current extras relation
            foreach ($extrasToRemove as $extraToRemove) {
                $modelExtra = $modelExtraRepository->findOneBy(['model' => $model, 'extra' => $extraToRemove]);
                if ($modelExtra != null) {
                    $isUsed = $em->getRepository(Preparation::class)->findOneBy(['modelExtra' => $modelExtra]);

                    if ($isUsed == null) {
                        $em->remove($modelExtra);
                    } else {
                        $isAlreadyUsed = true;
                    }
                }
            }

            $em->flush();

            if ($isAlreadyUsed) $this->addFlash('error', "Certains matériaux n'ont pas pu être retiré car ils sont actuellement utilisé dans une commande.");
            $this->addFlash('success', "Les changements ont bien été effectué.");
        }

        return $this->render("back/company/services/model_extras/manage.html.twig", [
            "model" => $model,
            "form" => $form->createView()
        ]);
    }
}