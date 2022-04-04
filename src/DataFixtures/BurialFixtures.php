<?php

namespace App\DataFixtures;

use App\Entity\Burial;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class BurialFixtures extends Fixture implements FixtureGroupInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        for($i = 0; $i < 5; $i++)
        {

            $burial = new Burial();
            $burial
                ->setName($faker->word())
                ->setDescription(' eros. Nullam eget ligula in nunc feugiat facilisis. Phasellus scelerisque gravida enim sed mattis. Nam non elit at libero cursus tempus sed eu felis');

            $manager->persist($burial);
        }

        $manager->flush();

    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}