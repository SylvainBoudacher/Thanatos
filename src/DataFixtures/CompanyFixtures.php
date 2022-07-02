<?php


namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class CompanyFixtures extends Fixture
{
    public const COMPANY_REFERENCE = "company-reference";
    public const DRIVER_REFERENCE = "driver-reference";

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        for($i = 0; $i < 20; $i++)
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
            $this->addReference(self::COMPANY_REFERENCE."-".$i, $company);

            $driver = new Company();
            $driver
              ->setType("DRIVER")
              ->setName($faker->company)
                ->setDescription(' eros. Nullam eget ligula in nunc feugiat facilisis. Phasellus sce
              lerisque gravida enim sed mattis. Nam non elit at libero cursus tempus sed eu felis')
                ->setSiret("66342889".$faker->randomNumber(6))
                ->setIban("FR4967292327851NCR1XQ".$faker->randomNumber(6))
                ->setRib("FR87654".$faker->randomNumber(6))
              ->setBic("BNPAGFGX");

            $manager->persist($driver);
            $this->addReference(self::DRIVER_REFERENCE."-".$i, $driver);
        }

        $companyTest = new Company();
        $companyTest
            ->setType("COMPANY")
            ->setName("Morgue test")
            ->setDescription('Description de la morgue test')
            ->setSiret("66342889".$faker->randomNumber(6))
            ->setIban("FR4967292327851NCR1XQ".$faker->randomNumber(6))
            ->setRib("FR87654".$faker->randomNumber(6))
            ->setBic("BNPAGFGX");
        $manager->persist($companyTest);
        $this->addReference(self::COMPANY_REFERENCE, $companyTest);

        $driverTest = new Company();
        $driverTest
            ->setType("DRIVER")
            ->setName("Driver test")
            ->setDescription('Description de driver test')
            ->setSiret("66342889".$faker->randomNumber(6))
            ->setIban("FR4967292327851NCR1XQ".$faker->randomNumber(6))
            ->setRib("FR87654".$faker->randomNumber(6))
            ->setBic("BNPAGFGX");
        $manager->persist($driverTest);
        $this->addReference(self::DRIVER_REFERENCE, $driverTest);

        $manager->flush();

    }
}