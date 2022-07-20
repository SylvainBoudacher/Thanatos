<?php

namespace App\Form;

use App\Entity\Painting;
use App\Utils\MediaConstraints;
use App\Utils\StyleClasses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaintingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
                "attr" => [
                    "placeholder" => "Nom de la nouvelle peinture...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
                ],
            ])
            ->add('hexaCode', ColorType::class, [
                "label" => "Code Hexadécimal de la couleur",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
            ])
            ->add('price', MoneyType::class, [
                "currency" => false,
                "html5" => true,
                "label" => "Prix",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT
                ],
                'constraints' => MediaConstraints::$IMAGE,

                "attr" => [
                    "placeholder" => "Entrez le prix de la peinture...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
                    'accept' => 'image/*'
                ]
            ])
            ->add('media', MediaType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Painting::class,
        ]);
    }
}
