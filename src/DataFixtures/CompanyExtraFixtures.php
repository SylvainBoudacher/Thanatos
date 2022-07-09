<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyExtra;
use App\Entity\Extra;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Faker;

class CompanyExtraFixtures extends Fixture implements DependentFixtureInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $extras = $manager->getRepository(Extra::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();

        foreach ($companies as $company) {
            foreach ($extras as $extra) {

                $companyExtra = new CompanyExtra();
                $companyExtra
                    ->setCompany($company)
                    ->setExtra($extra);
                $manager->persist($companyExtra);

            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CompanyFixtures::class,
            ExtraFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}