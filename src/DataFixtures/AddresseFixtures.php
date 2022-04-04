<?php

namespace App\DataFixtures;


use App\Entity\Address;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class AddresseFixtures extends Fixture
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        $address = new Address();
        $address
        ->setCity($faker->city)
        ->setPostcode($faker->postcode)
        ->setNumber($faker->numberBetween($min = 1, $max = 407))
        ->setStreet($faker->city);

        $manager->persist($address);          
    
        $manager->flush();

        $this->addReference("address", $address);
        
    }
}