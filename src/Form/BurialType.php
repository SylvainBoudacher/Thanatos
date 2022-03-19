<?php

namespace App\Form;

use App\Entity\Burial;
use App\Utils\StyleClasses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BurialType extends AbstractType
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
                    "placeholder" => "Nom de la tombe...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
                ],
            ])
            ->add('description', TextareaType::class, [
                "label" => "Description",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
                "attr" => [
                    "placeholder" => "Entrez la description de votre tombe...",
                    "class" => StyleClasses::TEXT_AREA_DEFAULT,
                    "rows" => "4"
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Burial::class,
        ]);
    }
}
