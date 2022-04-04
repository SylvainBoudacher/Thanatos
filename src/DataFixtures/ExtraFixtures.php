<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Extra;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Faker;

class ExtraFixtures extends Fixture implements FixtureGroupInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        for($i = 0; $i < 20; $i++)
        {
            $extra = new Extra();
            $extra
                ->setName($faker->word())
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ut feugiat elit, vitae tempor ligula. Suspendisse volutpat enim nec commodo lobortis. Ut et eleifend nulla. Proin dignissim elementum sollicitudin. Aliquam gravida vel mi euismod auctor. Ut vel rutrum enim, eget feugiat nunc. Sed aliquet ante si')
                ->setPrice($faker->numberBetween(10, 500));
            $manager->persist($extra);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}