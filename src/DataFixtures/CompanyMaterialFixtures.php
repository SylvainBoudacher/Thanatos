<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyMaterial;
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

class CompanyMaterialFixtures extends Fixture implements DependentFixtureInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $materials = $manager->getRepository(Material::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();

        foreach ($companies as $company) {
            foreach ($materials as $material) {

                $companyMaterial = new CompanyMaterial();
                $companyMaterial
                    ->setCompany($company)
                    ->setMaterial($material);
                $manager->persist($companyMaterial);

            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CompanyFixtures::class,
            MaterialFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}