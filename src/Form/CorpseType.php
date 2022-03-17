<?php

namespace App\Form;

use App\Config\PeopleSex;
use App\Config\SexPeople;
use App\Entity\Corpse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Positive;

class CorpseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Prénom"
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom de famille"
            ])
            ->add('birthdate', DateType::class, [
                'label' => "Année de naissance",
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('dayOfDeath', DateTimeType::class, [
                'label' => "Date de mort",
                'widget' => 'single_text'
            ])
            ->add(
                'sex',
                ChoiceType::class,
                ['choices' => [
                    Corpse::WOMEN => Corpse::WOMEN,
                    Corpse::MAN => Corpse::MAN,
                    Corpse::OTHER => Corpse::OTHER,
                ], 'required' => true, 'label' => "Sexe de la personne"]
            )
            ->add('causeOfDeath', TextareaType::class, [
                'label' => "Cause de la mort"
            ])
            ->add('weight', NumberType::class, [
                'label' => "Poids"
            ])
            ->add('height', NumberType::class, [
                'label' => "Taille",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Corpse::class,
        ]);
    }
}
