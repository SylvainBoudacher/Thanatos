<?php

namespace App\DataFixtures;

use App\Entity\Extra;
use App\Entity\Material;
use App\Entity\Model;
use App\Entity\ModelMaterial;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class ModelMaterialFixtures extends Fixture implements DependentFixtureInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $materials = $manager->getRepository(Material::class)->findAll();
        $models = $manager->getRepository(Model::class)->findAll();

        foreach ($models as $model) {
            foreach ($materials as $material) {

                $modelMaterial = new ModelMaterial();
                $modelMaterial
                    ->setModel($model)
                    ->setMaterial($material);

                $manager->persist($modelMaterial);

            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MaterialFixtures::class,
            ModelFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}