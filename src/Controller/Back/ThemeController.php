<?php

namespace App\Controller\Back;

use App\Entity\CompanyTheme;
use App\Entity\Preparation;
use App\Entity\Theme;
use App\Repository\CompanyRepository;
use App\Repository\CompanyThemeRepository;
use App\Repository\ThemeRepository;
use App\Repository\UserRepository;
use App\Security\Voter\GeneralVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/morgue/services/themes")]
#[IsGranted("ROLE_COMPANY")]
class ThemeController extends AbstractController
{
    #[Route('/', name: 'view_company_themes')]
    public function view_company_themes(ThemeRepository $themeRep, CompanyThemeRepository $companyThemeRep, UserRepository $userRep): Response
    {
        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        $themes = $themeRep->findBy([
            'deletedAt' => null,
        ], [
            'type' => 'ASC',
            'name' => 'ASC'
        ]);
        $themesSuscribedByCompany = $themeRep->getAllByCompany($company);

        $detailedThemes = [];

        foreach ($themes as $theme) {
            $detailedThemes[] = [
                "canBeSwitched" => $this->canBeSwitched($theme),
                "theme" => $theme,
                "isSuscribed" => in_array($theme, $themesSuscribedByCompany, true),
            ];
        }

        return $this->render("back/company/services/company_themes/index.html.twig", [
            "detailedThemes" => $detailedThemes
        ]);
    }

    private function canBeSwitched(Theme $theme): bool
    {

        $company = $this->getUser()->getCompany();

        $em = $this->getDoctrine()->getManager();
        $preparations = $em->getRepository(Preparation::class)->getPreparationsByCompany($company);

        return empty($preparations);
    }

    #[Route('/switch/{id}', name: 'switch_company_themes')]
    public function switch_company_themes(EntityManagerInterface $em, ThemeRepository $themeRep, Theme $theme, CompanyRepository $companyRep, UserRepository $userRep, CompanyThemeRepository $companyThemeRep): Response
    {

        $user = $userRep->find($this->getUser());
        $company = $user->getCompany();

        $this->denyAccessUnlessGranted(GeneralVoter::VIEW_EDIT, $theme);

        // TODO : Meilleur vérification plus tard (genre theme désactivé par thanatos)
        if (!empty($company)) {

            $companyTheme = $companyThemeRep->getOneByCompanyAndTheme($company, $theme);

            if ($companyTheme) {

                if (!$this->canBeSwitched($theme)) {
                    $this->addFlash("error", "Le thème " . $theme->getName() . " ne peut être retiré car il est utilisé dans une commande");
                    return $this->redirectToRoute("view_company_themes");
                }

                $em->remove($companyTheme);
                $em->flush();
                $this->addFlash("success", "Vous vous êtes désinscrit du thème " . $theme->getName() . ".");
                return $this->redirectToRoute("view_company_themes");
            }

            $companyTheme = new CompanyTheme();
            $companyTheme->setCompany($company);
            $companyTheme->setTheme($theme);

            $em->persist($companyTheme);
            $em->flush();
            $this->addFlash("success", "Vous vous êtes inscrit au thème " . $theme->getName() . ".");
            return $this->redirectToRoute("view_company_themes");
        }

        $this->addFlash("error", "Une erreur est survenue. Veuillez réessayez ultérieurement");
        return $this->redirectToRoute("view_company_themes");
    }


}