<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyExtra;
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

class CompanyExtraFixtures extends Fixture implements FixtureGroupInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $extras = $manager->getRepository(Extra::class)->findAll();
        $company = $manager->getRepository(Company::class)->find(3);

        for($i = 0; $i < 5; $i++)
        {

            $entity = new CompanyExtra();
            $entity
                ->setExtra($faker->randomElement($extras))
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