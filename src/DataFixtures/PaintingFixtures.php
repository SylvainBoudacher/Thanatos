<?php

namespace App\DataFixtures;

use App\Entity\Painting;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Faker;

class PaintingFixtures extends Fixture implements FixtureGroupInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        for($i = 0; $i < 20; $i++)
        {
            $hexColor = $faker->safeHexColor();

            $painting = new Painting();
            $painting
                ->setName($faker->safeColorName())
                ->setPrice($faker->numberBetween(100, 1000))
                ->setHexaCode(ltrim($hexColor, $hexColor[0]));
            $manager->persist($painting);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}