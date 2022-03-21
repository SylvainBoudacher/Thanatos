<?php

namespace App\DataFixtures;


use App\Entity\Media;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class MediaFixtures extends Fixture
{
  
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create("fr-FR");
        $imagesDirPath = '/images/';

        $mediaUser = new Media();
        $mediaUser
        ->setName("user-default")
        ->setPathname($imagesDirPath."user-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaUser);

        $mediaCompany = new Media();
        $mediaCompany
        ->setName("company-default")
        ->setPathname($imagesDirPath."company-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaCompany);

        $mediaDriver = new Media();
        $mediaDriver
        ->setName("driver-default")
        ->setPathname($imagesDirPath."driver-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaDriver);

        $mediaCorpse = new Media();
        $mediaCorpse
        ->setName("corpse-default")
        ->setPathname($imagesDirPath."corpse-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaCorpse);

        $mediaWarehouse = new Media();
        $mediaWarehouse
        ->setName("warehouse-default")
        ->setPathname($imagesDirPath."warehouse-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaWarehouse);

        $mediaTheme = new Media();
        $mediaTheme
        ->setName("warehouse-default")
        ->setPathname($imagesDirPath."theme-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaTheme);

        $mediaMaterial = new Media();
        $mediaMaterial
        ->setName("material-default")
        ->setPathname($imagesDirPath."material-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaMaterial);

        $mediaExtra = new Media();
        $mediaExtra
        ->setName("extra-default")
        ->setPathname($imagesDirPath."extra-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaExtra);

        $mediaPainting = new Media();
        $mediaPainting
        ->setName("painting-default")
        ->setPathname($imagesDirPath."painting-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaPainting);

        $mediaModel = new Media();
        $mediaModel
        ->setName("model-default")
        ->setPathname($imagesDirPath."model-default.png")
        ->setIsVideo(0);
        $manager->persist($mediaModel);
        
        $manager->flush();
        
        $this->addReference("MEDIA_USER_DEFAULT", $mediaUser);
        $this->addReference("MEDIA_COMPANY_DEFAULT", $mediaCompany);
        $this->addReference("MEDIA_DRIVER_DEFAULT", $mediaDriver);
        $this->addReference("MEDIA_CORPSE_DEFAULT", $mediaCorpse);
        $this->addReference("MEDIA_WAREHOUSE_DEFAULT", $mediaWarehouse);
        $this->addReference("MEDIA_THEME_DEFAULT", $mediaTheme);
        $this->addReference("MEDIA_MATERIAL_DEFAULT", $mediaMaterial);
        $this->addReference("MEDIA_EXTRA_DEFAULT", $mediaExtra);
        $this->addReference("MEDIA_PAINTING_DEFAULT", $mediaPainting);
        $this->addReference("MEDIA_MODEL_DEFAULT", $mediaModel);
    }
}