<?php



namespace App\DataFixtures;

use App\Entity\Material;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Faker;

class MaterialFixtures extends Fixture implements FixtureGroupInterface
{
    public const MATERIAL_REFERENCE = "material-reference";

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        $materialGold = new Material();
        $materialGold
            ->setName("Or")
            ->setPrice($faker->numberBetween(250, 1000));
        $manager->persist($materialGold);

        $materialSilver = new Material();
        $materialSilver
            ->setName("Argent")
            ->setPrice($faker->numberBetween(250, 1000));
        $manager->persist($materialSilver);

        $materialBronze = new Material();
        $materialBronze
            ->setName("Bronze")
            ->setPrice($faker->numberBetween(250, 1000));
        $manager->persist($materialBronze);

        $materialWood = new Material();
        $materialWood
            ->setName("Bois")
            ->setPrice($faker->numberBetween(250, 1000));
        $manager->persist($materialWood);

        $materialMetal = new Material();
        $materialMetal
            ->setName("Metal")
            ->setPrice($faker->numberBetween(250, 1000));
        $manager->persist($materialMetal);

        $materialPlastic = new Material();
        $materialPlastic
            ->setName("Plastic")
            ->setPrice($faker->numberBetween(250, 1000));
        $manager->persist($materialPlastic);

        $manager->flush();

        $this->addReference(self::MATERIAL_REFERENCE."-gold", $materialGold);
        $this->addReference(self::MATERIAL_REFERENCE."-silver", $materialSilver);
        $this->addReference(self::MATERIAL_REFERENCE."-bronze", $materialBronze);
        $this->addReference(self::MATERIAL_REFERENCE."-wood", $materialWood);
        $this->addReference(self::MATERIAL_REFERENCE."-metal", $materialMetal);
        $this->addReference(self::MATERIAL_REFERENCE."-plastic", $materialPlastic);

    }
    public static function getGroups(): array
    {
        return ['group1'];
    }
}
