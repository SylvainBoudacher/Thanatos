<?php

namespace App\DataFixtures;

use App\Entity\Burial;
use App\Entity\Company;
use App\Entity\User;
use App\Entity\Model;
use App\Repository\CompanyRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class ModelFixtures extends Fixture implements DependentFixtureInterface
{
    public const MODEL_REFERENCE = "model-reference";

    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create("fr-FR");
        $companies = $manager->getRepository(Company::class)->findBy(["type" => "COMPANY"]);
        $burials = $manager->getRepository(Burial::class)->findAll();

        $modelTypes = ["Rond", "Carré", "Triangle"];

        $count = 0;
        foreach ($companies as $company) {
            foreach ($burials as $burial) {
                foreach ($modelTypes as $modelType) {

                    $burialName = $burial->getName();
                    $model = new Model();
                    $model
                        ->setName( $burialName." ".$modelType)
                        ->setDescription("Description de ". $burialName." ".$modelType)
                        ->setPrice($faker->numberBetween(15, 60))
                        ->setCompany($company)
                        ->setBurial($burial);

                    $manager->persist($model);
                    $this->addReference(self::MODEL_REFERENCE."-".$count, $model);
                    $count++;

                }
            }
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