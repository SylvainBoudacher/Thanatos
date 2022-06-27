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

        for($i = 0; $i < 10; $i++)
        {
            $company = new Company();
            $company
              ->setType("COMPANY")
              ->setName($faker->company)
              ->setDescription(' eros. Nullam eget ligula in nunc feugiat facilisis. Phasellus sce
              lerisque gravida enim sed mattis. Nam non elit at libero cursus tempus sed eu felis')
              ->setSiret("66342889".$faker->randomNumber(6))
              ->setIban("FR4967292327851NCR1XQ".$faker->randomNumber(6))
              ->setRib("FR87654".$faker->randomNumber(6))
              ->setBic("BNPAGFGX");
            $manager->persist($company);

            $driver = new Company();
            $driver
              ->setType("DRIVER")
              ->setName($faker->company)
              ->setDescription("descriptoin du premier chauffeur")
                ->setSiret("66342889".$faker->randomNumber(6))
                ->setIban("FR4967292327851NCR1XQ".$faker->randomNumber(6))
                ->setRib("FR87654".$faker->randomNumber(6))
              ->setBic("BNPAGFGX");

            $manager->persist($driver);
            $manager->flush();
        }

        $this->addReference("COMPANY_COMPANY", $company);
        $this->addReference("COMPANY_DRIVER", $driver);
    }
}