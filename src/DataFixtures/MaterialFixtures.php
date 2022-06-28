<?php

/*

namespace App\DataFixtures;

use App\Entity\Material;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class MaterialFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        for($i = 0; $i < 20; $i++)
        {
            $material = new Material();
            $material
                ->setName($faker->word())
                ->setPrice($faker->numberBetween(250, 1000));
            $manager->persist($material);
        }
        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['group1'];
    }
}
*/