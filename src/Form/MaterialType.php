<?php

namespace App\Form;

use App\Entity\Material;
use App\Utils\StyleClasses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialType extends AbstractType
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
                    "placeholder" => "Nom du nouveau matériaux...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
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
            'data_class' => Material::class,
        ]);
    }
}
