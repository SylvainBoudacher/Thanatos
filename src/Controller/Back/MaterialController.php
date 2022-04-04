<?php

namespace App\Controller\Back;

use App\Entity\CompanyExtra;
use App\Entity\CompanyMaterial;
use App\Entity\Material;
use App\Form\MaterialType;
use App\Repository\CompanyExtraRepository;
use App\Repository\CompanyMaterialRepository;
use App\Repository\ExtraRepository;
use App\Repository\MaterialRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/company/services/materials")]
#[IsGranted("ROLE_COMPANY")]
class MaterialController extends AbstractController
{
    #[Route('/', name: 'view_materials')]
    public function view_materials(MaterialRepository $materialRep, UserRepository $userRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        $materialsSuscribedByCompany = $materialRep->getAllByCompany($company);

        return $this->render("back/company/services/materials/index.html.twig", [
            "materials" => $materialRep->findAll(),
            "materialsSuscribedByCompany" => $materialsSuscribedByCompany,
        ]);
    }
    
    
    /* THANATOS DATABASE RELATED */
    

    #[Route('/details/{id}', name: 'details_material')]
    public function details_materials(MaterialRepository $materialRep, int $id): Response
    {
        return $this->render("back/company/services/materials/details.html.twig", [
            "material" => $materialRep->find($id)
        ]);
    }

    #[Route('/create', name: 'create_material')]
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

    #[Route('/modify/{id}', name: 'modify_material')]
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

    
    /* EXPOSED TO THE CLIENTS */


    #[Route('/switch/{id}', name: 'switch_material')]
    public function switch_material(int $id, EntityManagerInterface $em, UserRepository $userRep, CompanyMaterialRepository $companyMaterialRep, MaterialRepository $materialRep) : Response {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();
        $material = $materialRep->find($id);

        // TODO : Meilleur vérification plus tard (genre theme désactivé par thanatos)
        if ($company === null) {
            $this->addFlash("error", "Une erreur est survenue. Veuillez réessayez ultérieurement");
            return $this->redirectToRoute("view_materials");
        }

        $companyMaterial = $companyMaterialRep->getOneByCompanyAndMaterial($company, $material);

        if ($companyMaterial) {
            $em->remove($companyMaterial);
            $em->flush();
            $this->addFlash("success", "Le matériaux ".$material->getName()." ne sera plus disponible pour les clients");
            return $this->redirectToRoute("view_materials");
        }

        $companyMaterial = new CompanyMaterial();
        $companyMaterial->setCompany($company);
        $companyMaterial->setMaterial($material);

        $em->persist($companyMaterial);
        $em->flush();

        $this->addFlash("success", "le matériaux ".$material->getName()." est désormais disponible pour les clients");
        return $this->redirectToRoute("view_materials");

    }

}