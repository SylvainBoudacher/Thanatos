<?php

namespace App\Form;

use App\Entity\Burial;
use App\Entity\Model;
use App\Utils\StyleClasses;
use App\Utils\MediaConstraints;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

class ModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        MediaConstraints::init();
//        dd(MediaConstraints::$IMAGE);
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
                "attr" => [
                    "placeholder" => "Nom du nouveau modèle...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
                ],
            ])
            ->add('description', TextareaType::class, [
                "label" => "Description",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
                "attr" => [
                    "placeholder" => "Entrez la description de votre nouveau modèle...",
                    "class" => StyleClasses::TEXT_AREA_DEFAULT,
                    "rows" => "4"
                ],
            ])
            ->add('price', MoneyType::class, [
                "currency" => false,
                "html5" => true,
                "label" => "Prix",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT
                ],
                "attr" => [
                    "placeholder" => "Entre le prix de votre nouveau modèle...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
                ]
            ])
            ->add('company')
            ->add('burial', EntityType::class, [
                "label" => "Tombe sur laquelle baser votre modèle",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
                "help" => "Si tu n'es pas satisfait avec les choix ci-dessus, tu peux aller en un créer un autre dans la section précédente \"Tombe\".",
                "help_attr" => [
                    "class" => StyleClasses::HELP_DEFAULT,
                ],
                "class" => Burial::class,
                "choice_label" => "name",
                "attr" => [
                    "class" => StyleClasses::SELECT_DEFAULT,
                ],
            ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                "attr" => [
                    "class" => "mt-4",
                    'accept' => 'image/*'

                ],
                'constraints' => MediaConstraints::$IMAGE,
                "help" => "ATTENTION : Les images utilisés seront TOUTES remplacées par celles que vous sélectionnerez maintenant",
                "help_attr" => [
                    "class" => StyleClasses::HELP_DEFAULT,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Model::class,
        ]);
    }
}
