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

class CorpseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('birthdate', DateType::class)
            ->add('dayOfDeath', DateTimeType::class)
            ->add('sex', ChoiceType::class, 
            ['choices' => [
                Corpse::WOMEN => Corpse::WOMEN,
                Corpse::MAN => Corpse::MAN,
                Corpse::OTHER => Corpse::OTHER,
            ], 'required' => true])
            ->add('causeOfDeath', TextareaType::class)
            ->add('weight', NumberType::class)
            ->add('height', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Corpse::class,
        ]);
    }
}
