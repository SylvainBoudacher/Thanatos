<?php

namespace App\Controller\Back;

use App\Entity\CompanyExtra;
use App\Entity\CompanyPainting;
use App\Entity\Extra;
use App\Entity\Preparation;
use App\Form\ExtraType;
use App\Repository\CompanyExtraRepository;
use App\Repository\CompanyPaintingRepository;
use App\Repository\ExtraRepository;
use App\Repository\PaintingRepository;
use App\Repository\UserRepository;
use App\Security\Voter\GeneralVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/services/extras")]
#[IsGranted("ROLE_COMPANY")]
class ExtraController extends AbstractController
{
    #[Route('/', name: 'view_extras')]
    public function view_extras(ExtraRepository $extraRep, UserRepository $userRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        $extrasSuscribedByCompany = $extraRep->getAllByCompany($company);
        $extras = $extraRep->findBy([
            'deletedAt' => null,
        ], [
            'name' => 'ASC'
        ]);

        foreach ($extras as $extra) {
            $extra->canBeDeleted = $this->canBeDeleted($extra);
            $extra->canBeSwitched = $this->canBeSwitched($extra);
        }

        foreach ($extrasSuscribedByCompany as $extra) {
            $extra->canBeSwitched = $this->canBeSwitched($extra);
        }

        return $this->render("back/company/services/extras/index.html.twig", [
            "extras" => $extras,
            "extrasSuscribedByCompany" => $extrasSuscribedByCompany
        ]);
    }


    /* THANATOS DATABASE RELATED */

    private function canBeDeleted(Extra $extra): bool
    {
        $em = $this->getDoctrine()->getManager();
        $preparations = $em->getRepository(Preparation::class)->findAll();

        if (!empty($preparations)) $preparations = array_map(function ($p) {
            if ($p->getModelMaterial() !== null)
                return $p->getModelMaterial()->getId();
        }, $preparations);

        return empty($extra->getModelExtras()->toArray()) && empty($extra->getCompanyExtras()->toArray()) && !in_array($extra->getId(), $preparations);
    }

    private function canBeSwitched(Extra $extra): bool
    {
        $em = $this->getDoctrine()->getManager();
        $preparations = $em->getRepository(Preparation::class)->findAll();

        if (!empty($preparations)) $preparations = array_map(function ($p) {
            if ($p->getModelMaterial() !== null)
                return $p->getModelMaterial()->getId();
        }, $preparations);

        return !in_array($extra->getId(), $preparations);
    }

    #[Route('/details/{id}', name: 'details_extra')]
    public function details_extra(ExtraRepository $extraRep, Extra $extra): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $extra);

        return $this->render("back/company/services/extras/details.html.twig", [
            "extra" => $extra
        ]);
    }

    #[Route('/créer', name: 'create_extra')]
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

    #[Route('/modifier/{id}', name: 'modify_extra')]
    public function modify_extras(Request $request, EntityManagerInterface $em, ExtraRepository $extraRep, Extra $extra): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $extra);

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


    /* EXPOSED TO THE CLIENTS */

    #[Route('/supprimer/{id}', name: 'delete_extra')]
    public function delete_extra(EntityManagerInterface $em, ExtraRepository $extraRep, Extra $extra): Response
    {
        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $extra);
        if (!$this->canBeDeleted($extra)) throw $this->createAccessDeniedException();

        if ($extra->getMedia() != null) $em->remove($extra->getMedia());
        $em->remove($extra);
        $em->flush();

        return $this->redirectToRoute("view_extras");
    }

    #[Route('/switch/{id}', name: 'switch_extra')]
    public function switch_extra(Extra $extra, EntityManagerInterface $em, UserRepository $userRep, CompanyExtraRepository $companyExtraRep, ExtraRepository $extraRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        // TODO : Meilleur vérification plus tard (genre theme désactivé par thanatos)
        if ($company === null) {
            $this->addFlash("error", "Une erreur est survenue. Veuillez réessayez ultérieurement");
            return $this->redirectToRoute("view_extras");
        }

        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $extra);

        $companyExtra = $companyExtraRep->getOneByCompanyAndExtra($company, $extra);

        if ($companyExtra) {
            if (!$this->canBeSwitched($extra)) {
                $this->addFlash("error", "L'extra " . $extra->getName() . " ne peut être retiré car il est utilisé dans une commande");
                return $this->redirectToRoute("view_extras");
            }
            $em->remove($companyExtra);
            $em->flush();
            $this->addFlash("success", "L'extra " . $extra->getName() . " ne sera plus disponible pour les clients");
            return $this->redirectToRoute("view_extras");
        }

        $companyExtra = new CompanyExtra();
        $companyExtra->setCompany($company);
        $companyExtra->setExtra($extra);

        $em->persist($companyExtra);
        $em->flush();

        $this->addFlash("success", "l'extra " . $extra->getName() . " est désormais disponible pour les clients");
        return $this->redirectToRoute("view_extras");

    }


}