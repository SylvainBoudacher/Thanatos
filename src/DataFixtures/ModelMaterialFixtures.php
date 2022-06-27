<?php
/*
namespace App\DataFixtures;

use App\Entity\Extra;
use App\Entity\Material;
use App\Entity\Model;
use App\Entity\ModelMaterial;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class ModelMaterialFixtures extends Fixture implements FixtureGroupInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $materials = $manager->getRepository(Material::class)->findAll();
        $models = $manager->getRepository(Model::class)->findAll();

        for($i = 0; $i < 5; $i++)
        {

            $modelMaterial = new ModelMaterial();
            $modelMaterial
                ->setModel($faker->randomElement($models))
                ->setMaterial($faker->randomElement($materials));
            $manager->persist($modelMaterial);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}*/