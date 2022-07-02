<?php

namespace App\DataFixtures;

use App\Entity\Extra;
use App\Entity\Model;
use App\Entity\ModelExtra;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Faker;

class ModelExtraFixtures extends Fixture implements DependentFixtureInterface
{

    public const MODEL_EXTRA_REFERENCE = "model-extra-reference";
  
    public function load(ObjectManager $manager)
    {
        $extras = $manager->getRepository(Extra::class)->findAll();
        $models = $manager->getRepository(Model::class)->findAll();
        $faker = Faker\Factory::create("fr-FR");

        foreach ($models as $model) {
            foreach ($extras as $extra) {

                if ($faker->numberBetween(0, 1) === 0) {
                    $modelExtra = new ModelExtra();
                    $modelExtra
                        ->setModel($model)
                        ->setExtra($extra);

                    $manager->persist($modelExtra);

                }
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ModelFixtures::class,
            ExtraFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}