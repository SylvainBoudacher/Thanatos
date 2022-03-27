<?php

namespace App\Controller\Back;

use App\Entity\CompanyExtra;
use App\Entity\CompanyPainting;
use App\Entity\Extra;
use App\Form\ExtraType;
use App\Repository\CompanyExtraRepository;
use App\Repository\CompanyPaintingRepository;
use App\Repository\ExtraRepository;
use App\Repository\PaintingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/company/services/extras")]
#[IsGranted("ROLE_COMPANY")]
class ExtraController extends AbstractController
{
    #[Route('/', name: 'view_extras')]
    public function view_extras(ExtraRepository $extraRep, UserRepository $userRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        $extrasSuscribedByCompany = $extraRep->getAllByCompany($company);

        return $this->render("back/company/services/extras/index.html.twig", [
            "extras" => $extraRep->findAll(),
            "extrasSuscribedByCompany" => $extrasSuscribedByCompany
        ]);
    }

    
    /* THANATOS DATABASE RELATED */

    
    #[Route('/details/{id}', name: 'details_extra')]
    public function details_extra(ExtraRepository $extraRep, int $id): Response
    {
        return $this->render("back/company/services/extras/details.html.twig", [
            "extra" => $extraRep->find($id)
        ]);
    }

    #[Route('/create', name: 'create_extra')]
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

    #[Route('/modify/{id}', name: 'modify_extra')]
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

    #[Route('/delete/{id}', name: 'delete_extra')]
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

    
    /* EXPOSED TO THE CLIENTS */

    
    #[Route('/switch/{id}', name: 'switch_extra')]
    public function switch_extra(int $id, EntityManagerInterface $em, UserRepository $userRep, CompanyExtraRepository $companyExtraRep, ExtraRepository $extraRep) : Response {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();
        $extra = $extraRep->find($id);

        // TODO : Meilleur vérification plus tard (genre theme désactivé par thanatos)
        if ($company === null) {
            $this->addFlash("error", "Une erreur est survenue. Veuillez réessayez ultérieurement");
            return $this->redirectToRoute("view_extras");
        }

        $companyExtra = $companyExtraRep->getOneByCompanyAndExtra($company, $extra);

        if ($companyExtra) {
            $em->remove($companyExtra);
            $em->flush();
            $this->addFlash("success", "L'extra ".$extra->getName()." ne sera plus disponible pour les clients");
            return $this->redirectToRoute("view_extras");
        }

        $companyExtra = new CompanyExtra();
        $companyExtra->setCompany($company);
        $companyExtra->setExtra($extra);

        $em->persist($companyExtra);
        $em->flush();

        $this->addFlash("success", "l'extra ".$extra->getName()." est désormais disponible pour les clients");
        return $this->redirectToRoute("view_extras");

    }

}