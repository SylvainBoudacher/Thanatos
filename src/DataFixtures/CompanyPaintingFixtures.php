<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyPainting;
use App\Entity\Extra;
use App\Entity\Material;
use App\Entity\Model;
use App\Entity\ModelMaterial;
use App\Entity\Painting;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class CompanyPaintingFixtures extends Fixture implements DependentFixtureInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $companies = $manager->getRepository(Company::class)->findAll();
        $paintings = $manager->getRepository(Painting::class)->findAll();

        foreach ($companies as $company) {
            foreach ($paintings as $painting) {

                if ($faker->numberBetween(0, 1) === 0) {

                    $companyPainting = new CompanyPainting();
                    $companyPainting
                        ->setCompany($company)
                        ->setPainting($painting);

                    $manager->persist($companyPainting);
                }

            }
        }

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            CompanyFixtures::class,
            PaintingFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}