<?php

namespace App\Form;

use App\Entity\Theme;
use App\Utils\StyleClasses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeType extends AbstractType
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
                    "placeholder" => "Nom du nouveau thème...",
                    "class" => StyleClasses::TEXT_INPUT_DEFAULT,
                ],
            ])
            ->add('description',TextareaType::class, [
                "label" => "Description",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
                "attr" => [
                    "placeholder" => "Entrez la description du nouveau thème...",
                    "class" => StyleClasses::TEXT_AREA_DEFAULT,
                    "rows" => "4"
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    "Classique" => Theme::TYPE_CLASSIC,
                    'Spéciale' => Theme::TYPE_SPECIAL,
                ],
                "label" => "Type",
                "label_attr" => [
                    "class" => StyleClasses::lABEL_DEFAULT,
                ],
                "help" => "Attention : Les offres spéciales ne renverront jamais les corps à leurs propriétaires respectifs.",
                "help_attr" => [
                    "class" => StyleClasses::HELP_DEFAULT,
                ],
//                "choice_label" => "name",
                "attr" => [
                    "class" => StyleClasses::SELECT_DEFAULT,
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
            ->add('media', MediaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Theme::class,
        ]);
    }
}
