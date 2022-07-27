<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;



class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public const USER_REFERENCE = "user-reference";

    public const ROLE_USER = "ROLE_USER";
    public const ROLE_ADMIN = "ROLE_ADMIN";
    public const ROLE_COMPANY = "ROLE_COMPANY";
    public const ROLE_DRIVER = "ROLE_DRIVER";


    public function load(ObjectManager $manager)
    {
        $roles = ["ROLE_USER" , "ROLE_DRIVER" , "ROLE_COMPANY"];
        $faker = Faker\Factory::create("fr-FR");

        $userTest = new User();
        $userTest->setFirstname('Test');
        $userTest->setLastname('Testouille');
        $userTest->setEmail('test@test.com');
        $password = $this->hasher->hashPassword($userTest, '@Test123');
        $userTest->setPassword($password);
        $userTest->setRoles([]);
        $userTest->setAddress($this->getReference(AddresseFixtures::ADDRESS_REFERENCE."-0"));

        $manager->persist($userTest);




        $admin = new User();
        $admin->setFirstname('Test');
        $admin->setLastname('Testouille');
        $admin->setEmail('admin@test.com');
        $password = $this->hasher->hashPassword($admin, '@Test123');
        $admin->setPassword($password);
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setAddress($this->getReference(AddresseFixtures::ADDRESS_REFERENCE."-1"));

        $manager->persist($admin);


        $driver = new User();
        $driver->setFirstname('Test');
        $driver->setLastname('Testouille');
        $driver->setEmail('driver@test.com');
        $password = $this->hasher->hashPassword($driver, '@Test123');
        $driver->setPassword($password);
        $driver->setRoles(["ROLE_DRIVER"]);
        $driver->setAddress($this->getReference(AddresseFixtures::ADDRESS_REFERENCE."-2"));
        $driver->setCompany($this->getReference(CompanyFixtures::DRIVER_REFERENCE));

        $manager->persist($driver);


        $company = new User();
        $company->setFirstname('Test');
        $company->setLastname('Testouille');
        $company->setEmail('company@test.com');
        $password = $this->hasher->hashPassword($company, '@Test123');
        $company->setPassword($password);
        $company->setRoles(["ROLE_COMPANY"]);
        $company->setAddress($this->getReference(AddresseFixtures::ADDRESS_REFERENCE."-3"));
        $company->setCompany($this->getReference(CompanyFixtures::COMPANY_REFERENCE));


        $manager->persist($company);

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($this->hasher->hashPassword($userTest, '@Test123'))
                ->setRoles([$roles[mt_rand(0, 2)]])
                ->setAddress($this->getReference(AddresseFixtures::ADDRESS_REFERENCE."-".$i));

            $role = $user->getRoles()[0];



            if ($role === self::ROLE_COMPANY) {
                $user->setCompany($this->getReference(CompanyFixtures::COMPANY_REFERENCE."-".$i));
            } elseif ($role === self::ROLE_DRIVER) {
                $user->setCompany($this->getReference(CompanyFixtures::DRIVER_REFERENCE."-".$i));
            }

            $manager->persist($user);

            $this->addReference(self::USER_REFERENCE."-".$i, $user);
        }


        $manager->flush();

        $this->addReference(self::USER_REFERENCE."-user", $userTest);
        $this->addReference(self::USER_REFERENCE."-admin", $admin);
        $this->addReference(self::USER_REFERENCE."-driver", $driver);
        $this->addReference(self::USER_REFERENCE."-company", $company);
    }
}