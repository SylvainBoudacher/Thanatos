<?php

namespace App\DataFixtures;

use App\Entity\Burial;
use App\Entity\Company;
use App\Entity\User;
use App\Entity\Model;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class ModelFixtures extends Fixture implements FixtureGroupInterface
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $users = $manager->getRepository(User::class)->findBy(['id' => 1]);
        $burials = $manager->getRepository(Burial::class)->findAll();
        for($i = 0; $i < 5; $i++)
        {
            $model = new Model();
            $model
                ->setName($faker->word())
                ->setDescription('is sit amet ex mollis mauris congue rhoncus at sed lorem. Nulla facilisi. Nulla non luctus eros. Curabitur a feugiat diam. Duis laoreet porttitor tortor sit amet dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean faucibus sit amet augue quis ullamcorper. V')
                ->setPrice($faker->numberBetween(1000, 10000))
                ->setCompany($faker->randomElement(array_column($users, 'company')))
                ->setBurial($faker->randomElement($burials));

            $manager->persist($model);
        }

        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            CompanyFixtures::class,
            BurialFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}