<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyTheme;
use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyThemeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $companies = $manager->getRepository(Company::class)->findAll();
        $themes = $manager->getRepository(Theme::class)->findAll();

        foreach ($companies as $company) {
            foreach ($themes as $theme) {

                $companyTheme = new CompanyTheme();
                $companyTheme
                    ->setCompany($company)
                    ->setTheme($theme);
                $manager->persist($companyTheme);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CompanyFixtures::class,
            ThemeFixtures::class
        ];
    }

}