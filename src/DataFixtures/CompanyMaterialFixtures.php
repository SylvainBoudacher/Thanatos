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
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class CompanyMaterialFixtures extends Fixture implements FixtureGroupInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $materials = $manager->getRepository(Material::class)->findAll();
        $company = $manager->getRepository(Company::class)->find(3);

        for($i = 0; $i < 5; $i++)
        {

            $entity = new CompanyMaterial();
            $entity
                ->setMaterial($faker->randomElement($materials))
                ->setCompany($company);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}