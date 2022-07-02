<?php

namespace App\DataFixtures;

use App\Entity\Burial;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class BurialFixtures extends Fixture implements FixtureGroupInterface
{
    public const BURIAL_REFERENCE = "burial-reference";

    public function load(ObjectManager $manager)
    {

        $burialTomb = new Burial();
        $burialTomb
           ->setName("Tombe")
           ->setDescription("Description de la tombe");
        $manager->persist($burialTomb);

        $burialUrn = new Burial();
        $burialUrn
            ->setName("Urne")
            ->setDescription("Description de l'urne");
        $manager->persist($burialUrn);

        $burialTinCan = new Burial();
        $burialTinCan
            ->setName("Boite de conserve")
            ->setDescription("Description de la boite de conserve");
        $manager->persist($burialTinCan);

        $manager->flush();

        $this->addReference(self::BURIAL_REFERENCE."-tomb", $burialTomb);
        $this->addReference(self::BURIAL_REFERENCE."-urn", $burialUrn);
        $this->addReference(self::BURIAL_REFERENCE."-tin-can", $burialTinCan);

    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}