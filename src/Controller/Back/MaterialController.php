<?php

namespace App\Controller\Back;

use App\Entity\CompanyExtra;
use App\Entity\CompanyMaterial;
use App\Entity\Material;
use App\Entity\Preparation;
use App\Form\MaterialType;
use App\Repository\CompanyExtraRepository;
use App\Repository\CompanyMaterialRepository;
use App\Repository\ExtraRepository;
use App\Repository\MaterialRepository;
use App\Repository\ModelMaterialRepository;
use App\Repository\UserRepository;
use App\Security\Voter\GeneralVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/services/materiaux")]
#[IsGranted("ROLE_COMPANY")]
class MaterialController extends AbstractController
{
    #[Route('/', name: 'view_materials')]
    public function view_materials(MaterialRepository $materialRep, UserRepository $userRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        $materialsSuscribedByCompany = $materialRep->getAllByCompany($company);
        $materials = $materialRep->findBy([
            'deletedAt' => null,
        ], [
            'name' => 'ASC'
        ]);

        foreach ($materials as $material) {
            $material->canBeDeleted = $this->canBeDeleted($material);
            $material->canBeSwitched = $this->canBeSwitched($material);

        }

        foreach ($materialsSuscribedByCompany as $material) {
            $material->canBeSwitched = $this->canBeSwitched($material);
        }

        return $this->render("back/company/services/materials/index.html.twig", [
            "materials" => $materials,
            "materialsSuscribedByCompany" => $materialsSuscribedByCompany,
        ]);
    }


    /* THANATOS DATABASE RELATED */

    private function canBeDeleted(Material $material): bool
    {
        $em = $this->getDoctrine()->getManager();
        $preparations = $em->getRepository(Preparation::class)->findAll();

        if (!empty($preparations)) $preparations = array_map(function ($p) {
            if ($p->getModelMaterial() !== null)
                return $p->getModelMaterial()->getId();
        }, $preparations);

        return empty($material->getModelMaterials()->toArray()) && empty($material->getCompanyMaterials()->toArray()) && !in_array($material->getId(), $preparations);
    }

    private function canBeSwitched(Material $material): bool
    {
        $em = $this->getDoctrine()->getManager();
        $preparations = $em->getRepository(Preparation::class)->findAll();

        if (!empty($preparations)) $preparations = array_map(function ($p) {
            if ($p->getModelMaterial() !== null)
                return $p->getModelMaterial()->getId();
        }, $preparations);

        return !in_array($material->getId(), $preparations);
    }

    #[Route('/details/{id}', name: 'details_material')]
    public function details_materials(Material $material): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $material);

        return $this->render("back/company/services/materials/details.html.twig", [
            "material" => $material
        ]);
    }

    #[Route('/créer', name: 'create_material')]
    public function create_materials(Request $request, EntityManagerInterface $em): Response
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

    #[Route('/modifier/{id}', name: 'modify_material')]
    public function modify_materials(Request $request, EntityManagerInterface $em, MaterialRepository $materialRep, Material $material): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $material);

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


    /* EXPOSED TO THE CLIENTS */

    #[Route('/supprimer/{id}', name: 'delete_material')]
    public function delete_materials(EntityManagerInterface $em, MaterialRepository $materialRep, Material $material): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $material);

        if (!$this->canBeDeleted($material)) throw $this->createAccessDeniedException();

        if ($material->getMedia() != null) $em->remove($material->getMedia());
        $em->remove($material);
        $em->flush();

        return $this->redirectToRoute("view_materials");
    }

    #[Route('/switch/{id}', name: 'switch_material')]
    public function switch_material(Material $material, EntityManagerInterface $em, UserRepository $userRep, CompanyMaterialRepository $companyMaterialRep, MaterialRepository $materialRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        // TODO : Meilleur vérification plus tard (genre theme désactivé par thanatos)
        if ($company === null) {
            $this->addFlash("error", "Une erreur est survenue. Veuillez réessayez ultérieurement");
            return $this->redirectToRoute("view_materials");
        }

        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $material);

        $companyMaterial = $companyMaterialRep->getOneByCompanyAndMaterial($company, $material);

        if ($companyMaterial) {
            if (!$this->canBeSwitched($material)) {
                $this->addFlash("error", "Le matériaux " . $material->getName() . " ne peut être retiré car il est utilisé dans une commande");
                return $this->redirectToRoute("view_materials");
            }
            $em->remove($companyMaterial);
            $em->flush();
            $this->addFlash("success", "Le matériaux " . $material->getName() . " ne sera plus disponible pour les clients");
            return $this->redirectToRoute("view_materials");
        }

        $companyMaterial = new CompanyMaterial();
        $companyMaterial->setCompany($company);
        $companyMaterial->setMaterial($material);

        $em->persist($companyMaterial);
        $em->flush();

        $this->addFlash("success", "le matériaux " . $material->getName() . " est désormais disponible pour les clients");
        return $this->redirectToRoute("view_materials");

    }

}