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
  
    public function load(ObjectManager $manager)
    {
        $roles = ["ROLE_USER" , "ROLE_DRIVER" , "ROLE_COMPANY"];
        $faker = Faker\Factory::create("fr-FR");

        $user = new User();
        $user->setFirstname('Test');
        $user->setLastname('Testouille');
        $user->setEmail('test@test.com');
        $password = $this->hasher->hashPassword($user, '@Test123');
        $user->setPassword($password);
        $user->setRoles(["ROLE_ADMIN"]);


        $manager->persist($user);
        $manager->flush();

        // create 20 Users objects
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($faker->password)
                ->setRoles([$roles[mt_rand(0, 2)]])
                ->setAddress($this->getReference(AddresseFixtures::ADDRESS_REFERENCE));
            $manager->persist($user);
            $manager->flush();
        }

        $this->addReference("user", $user);








    }
}