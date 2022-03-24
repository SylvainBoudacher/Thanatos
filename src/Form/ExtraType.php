<?php

namespace App\Form;

use App\Entity\Extra;
use App\Utils\StyleClasses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtraType extends AbstractType
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
                    "placeholder" => "Nom du nouvel extra...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
                ],
            ])
            ->add('description', TextareaType::class, [
                "label" => "Description",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
                "attr" => [
                    "placeholder" => "Entrez la description de votre extra...",
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
                    "placeholder" => "Entrez le prix du matériaux...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
                ]
            ])
            ->add('media', MediaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Extra::class,
        ]);
    }
}
