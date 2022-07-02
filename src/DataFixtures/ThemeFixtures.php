<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ThemeFixtures extends Fixture
{

    public const THEME_REFERENCE = 'theme-reference';

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");

        // Classics

        $themeClassic = new Theme();
        $themeClassic
            ->setName("Classique")
            ->setDescription("Ni plus ni moins, la qualité minimum garantis par les normes de Thanatos")
            ->setType("classic")
            ->setPrice($faker->numberBetween(50, 500));
        $manager->persist($themeClassic);

        $themeBobo = new Theme();
        $themeBobo
            ->setName("Bobo Goldé")
            ->setDescription("Tu as trop d'argent et tu te sens d'humeur charitable ? Partages les avec nous car tu ne pourras pas emporter ton or dans l'eau delà")
            ->setType("classic")
            ->setPrice($faker->numberBetween(2000, 5000));
        $manager->persist($themeBobo);

        $themePeste = new Theme();
        $themePeste
            ->setName("Pestiférée")
            ->setDescription("Parce que tu es un rat, un vrai et t'as pas beaucoup d'argent. Ne t'attends pas à ce que mamie soit bien traité...")
            ->setType("classic")
            ->setPrice($faker->numberBetween(5, 45));
        $manager->persist($themePeste);

        $themeLove = new Theme();
        $themeLove
            ->setName("Tendrement votre")
            ->setDescription("Parce que la perte de votre amour est une étape douloureuse, nous ferons en sorte de lui donner ce qu'elle mérite une dernière fois.")
            ->setType("classic")
            ->setPrice($faker->numberBetween(600, 1000));
        $manager->persist($themeLove);

        // Specials

        $themeSpace = new Theme();
        $themeSpace
            ->setName("Vers l'infinie et l'au dela !")
            ->setDescription("Ok. C'est un peu cher, mais vous pouvez envoyer papy dans l'espace : avouez que c'est mega stylé non ?")
            ->setType("special")
            ->setPrice(20000);
        $manager->persist($themeSpace);

        $themePit = new Theme();
        $themePit
            ->setName("Dans la fosse")
            ->setDescription("Pourquoi se prendre la tête avec un packaging tout bien fait alors qu'on peut balancer ça vite fait bien fait dans une fosse commune ?")
            ->setType("special")
            ->setPrice(15);
        $manager->persist($themePit);

        $themePit = new Theme();
        $themePit
            ->setName("Sa place est dans un musée")
            ->setDescription("La panthéon des morts : nous avons notre propre musée d'exposition : Pour mettre en lumière les personnes qui vous importent le plus ici-bas")
            ->setType("special")
            ->setPrice(100000);
        $manager->persist($themePit);

        $themeChef = new Theme();
        $themeChef
            ->setName("Top Chef")
            ->setDescription("L'écologie c'est important : ici pas de gachis ! Participez à la réputation de la meilleure viande bovine nationale")
            ->setType("special")
            ->setPrice(25);
        $manager->persist($themeChef);

        $manager->flush();

        $this->setReference(self::THEME_REFERENCE."-classic", $themeClassic);
        $this->setReference(self::THEME_REFERENCE."-bobo", $themeBobo);
        $this->setReference(self::THEME_REFERENCE."-peste", $themePeste);
        $this->setReference(self::THEME_REFERENCE."-love", $themeLove);
        $this->setReference(self::THEME_REFERENCE."-space", $themeSpace);
        $this->setReference(self::THEME_REFERENCE."-pit", $themePit);
        $this->setReference(self::THEME_REFERENCE."-chef", $themeChef);

    }

}