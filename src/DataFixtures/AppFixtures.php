<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        for ($i = 0; $i < 20; $i++) {
            $address = new Address();
            $address
            ->setCity("mon gro boulde ville")
            ->setPostcode("91000")
            ->setNumber("69")
            ->setStreet("Rue de connard");
            $manager->persist($address);          
        }

        $manager->flush();
    }
}
