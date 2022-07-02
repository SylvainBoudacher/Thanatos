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

    public const EXTRA_REFERENCE = "extra-reference";
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        $extraHat = new Extra();
        $extraHat
            ->setName("Chapi Chapeau")
            ->setDescription('Description du chapi chapeau')
            ->setPrice($faker->numberBetween(10, 500));
        $manager->persist($extraHat);
        $this->addReference(self::EXTRA_REFERENCE."-hat", $extraHat);

        $extraSombrero = new Extra();
        $extraSombrero
            ->setName("Sombrero")
            ->setDescription('Description du Sombrero')
            ->setPrice($faker->numberBetween(10, 500));
        $manager->persist($extraSombrero);
        $this->addReference(self::EXTRA_REFERENCE."-sombrero", $extraSombrero);

        $extraMedal = new Extra();
        $extraMedal
            ->setName("Medal")
            ->setDescription('Description de la médaille')
            ->setPrice($faker->numberBetween(10, 500));
        $manager->persist($extraMedal);
        $this->addReference(self::EXTRA_REFERENCE."-medal", $extraMedal);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}