<?php

namespace App\Utils;

use DateTime;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class MediaConstraints
{

    public static $IMAGE = null;

    public static function init()
    {
        /* self::$IMAGE = new File([
             'maxSize' => '3M',
             'mimeTypes' => [
                 'image/jpeg',
                 'image/png',
             ],
             'mimeTypesMessage' => 'Veuillez télécharger des images valides.',
             'maxSizeMessage' => 'Veuillez télécharger des images d\’une taille inférieur à {{ limit }}'
         ]);*/

        self::$IMAGE = new All([
            new Image([
                'mimeTypesMessage' => 'Veuillez télécharger des images valides.',
                'maxSizeMessage' => 'Veuillez télécharger des images d\’une taille inférieur à {{ limit }}'
            ])]);
    }
}