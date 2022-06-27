<?php


namespace App\DataFixtures;


use App\Entity\Address;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class AddresseFixtures extends Fixture
{
    public const ADDRESS_REFERENCE = 'address-reference';

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        for($i = 0; $i < 50; $i++)
        {
            $address = new Address();
            $address
                ->setCity($faker->city)
                ->setPostcode(substr($faker->postcode, 0, 5))
                ->setNumber($faker->numberBetween($min = 1, $max = 407))
                ->setStreet($faker->city);
            $manager->persist($address);
            $manager->flush();
        }

        /*$this->addReference("address", $address);*/
        $this->addReference(self::ADDRESS_REFERENCE, $address);
    }
}
