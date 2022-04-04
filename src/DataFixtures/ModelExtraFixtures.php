<?php

namespace App\DataFixtures;

use App\Entity\Burial;
use App\Entity\Extra;
use App\Entity\Material;
use App\Entity\Model;
use App\Entity\ModelExtra;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class ModelExtraFixtures extends Fixture implements FixtureGroupInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $extras = $manager->getRepository(Extra::class)->findAll();
        $models = $manager->getRepository(Model::class)->findAll();

        for($i = 0; $i < 5; $i++)
        {

            $modelExtra = new ModelExtra();
            $modelExtra
                ->setExtra($faker->randomElement($extras))
                ->setModel($faker->randomElement($models));
            $manager->persist($modelExtra);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}