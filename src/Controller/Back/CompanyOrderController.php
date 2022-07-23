<?php

namespace App\Controller\Back;

use App\Entity\Preparation;
use App\Repository\PreparationRepository;
use App\Repository\UserRepository;
use App\Security\Voter\PreparationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/commandes")]
#[IsGranted("ROLE_COMPANY")]
class CompanyOrderController extends AbstractController
{
    private $userRep;

    public function __construct(UserRepository $userRep)
    {
        $this->userRep = $userRep;
    }

    /* #[Route('/test', name: 'test', methods: ['GET'])]
     public function page_error()
     {
         return $this->render('bundles/TwigBundle/Exception/error.html.twig');
     }*/

    #[Route('/', name: 'company_orders', methods: ['GET'])]
    public function dashboard(PreparationRepository $preparationRepository): Response
    {

        $company = $this->getCompanyOfUser();
        $preparations = $preparationRepository->getPreparationsByCompanyByStatus($company, [
            Preparation::FUNERAL_NEW,
            Preparation::FUNERAL_ACCEPT,
            Preparation::FUNERAL_DRIVER_ACCEPT_TO_BRINGS_TO_FUNERAL,
            Preparation::FUNERAL_DRIVER_BRINGS_TO_FUNERAL,
            Preparation::FUNERAL_CORPSE_ARRIVES_TO_FUNERAL,
            Preparation::FUNERAL_IN_PROGRESS_PROCESSING,
        ]);

        $preparationsClose = $preparationRepository->getPreparationsByCompanyByStatus($company, [
            Preparation::FUNERAL_CANCEL,
            Preparation::FUNERAL_CLOSE_PROCESSING
        ]);

        return $this->render('front/morgue/orders/index.html.twig', [
            'preparations' => $preparations,
            'preparationsClose' => $preparationsClose
        ]);
    }

    private function getCompanyOfUser()
    {

        $user = $this->userRep->find($this->getUser());
        $company = $user->getCompany();

        if ($company == null) throw $this->createAccessDeniedException();

        return $company;
    }

    #[Route('/{id}', name: 'company_order', methods: ['GET'])]
    public function command(PreparationRepository $preparationRepository, Preparation $preparation): Response
    {
        $this->denyAccessUnlessGranted(PreparationVoter::VIEW, $preparation);

        return $this->render('front/morgue/orders/command.html.twig', [
            'preparation' => $preparation,
        ]);
    }

    #[Route('/status-changer/{id}', name: 'company_order_change_status', methods: ['GET'])]
    public function change_status(Request $request, EntityManagerInterface $em, PreparationRepository $preparationRepository, Preparation $preparation): Response
    {
        $this->denyAccessUnlessGranted(PreparationVoter::EDIT, $preparation);

        if ($request->query->getBoolean('cancel') && $preparation->getStatus() == Preparation::FUNERAL_NEW) { // preparation cancel

            $preparation->setStatus(Preparation::FUNERAL_CANCEL);
            $em->flush();

            $this->addFlash('success', 'Commande numéro ' . $preparation->getId() . ' a bien été annulée');
            return $this->redirectToRoute('company_orders');

        } else if ( // preparation change status

            $preparation->getStatus() == Preparation::FUNERAL_NEW ||
            $preparation->getStatus() == Preparation::FUNERAL_DRIVER_BRINGS_TO_FUNERAL ||
            $preparation->getStatus() == Preparation::FUNERAL_CORPSE_ARRIVES_TO_FUNERAL ||
            $preparation->getStatus() == Preparation::FUNERAL_IN_PROGRESS_PROCESSING

        ) {

            if ($preparation->getStatus() == Preparation::FUNERAL_NEW) $preparation->setStatus(Preparation::FUNERAL_ACCEPT);
            else if ($preparation->getStatus() == Preparation::FUNERAL_DRIVER_BRINGS_TO_FUNERAL) $preparation->setStatus(Preparation::FUNERAL_CORPSE_ARRIVES_TO_FUNERAL);
            else if ($preparation->getStatus() == Preparation::FUNERAL_CORPSE_ARRIVES_TO_FUNERAL) $preparation->setStatus(Preparation::FUNERAL_IN_PROGRESS_PROCESSING);
            else if ($preparation->getStatus() == Preparation::FUNERAL_IN_PROGRESS_PROCESSING) $preparation->setStatus(Preparation::FUNERAL_CLOSE_PROCESSING);

            $this->addFlash('success', "La commande numéro " . $preparation->getId() . " a bien changée de statut");
            $em->flush();

        }

        return $this->render('front/morgue/orders/command.html.twig', [
            'preparation' => $preparation,
        ]);
    }


}
