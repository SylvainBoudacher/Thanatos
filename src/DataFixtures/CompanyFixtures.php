<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class CompanyFixtures extends Fixture
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        $company = new Company();
        $company
          ->setType("company")
          ->setName("Première morgue")
          ->setDescription("descriptoin de la première morgue")
          ->setSiret("66342889425929")
          ->setIban("FR4967292327851NCR1XQ625488")
          ->setRib("FR87654321098")
          ->setBic("BNPAGFGX");
        $manager->persist($company);

        $driver = new Company();
        $driver
          ->setType("driver")
          ->setName("Premier chauffeur")
          ->setDescription("descriptoin du premier chauffeur")
          ->setSiret("66342889425929")
          ->setIban("FR4967292327851NCR1XQ625488")
          ->setRib("FR87654321098")
          ->setBic("BNPAGFGX");
        $manager->persist($driver);

        $manager->flush();

        $this->addReference("COMPANY_COMPANY", $company);
        $this->addReference("COMPANY_DRIVER", $driver);
        
    }
}