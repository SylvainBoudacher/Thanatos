<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'attr' => array(
                    'placeholder' => 'Enfer'
                )
            ])
            ->add('description')
            ->add('siret', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Entrer un siret']),
                    new Regex('/^[0-9]{14}$/')
                ],
                'attr' => array(
                    'placeholder' => '12356894100056'
                )
            ])

            ->add('iban', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Entrer un iban']),
                    new Regex('/^([A-Z]{2}[ \-]?[0-9]{2})(?=(?:[ \-]?[A-Z0-9]){9,30}$)((?:[ \-]?[A-Z0-9]{3,5}){2,7})([ \-]?[A-Z0-9]{1,3})?$/')
                ],
                'attr' => array(
                    'placeholder' => 'FR7630003035409876543210925'
                )
            ])
            ->add('rib', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Entrer un rib']),
                    new Regex('/(?<B>\d{5})(?<G>\d{5})(?<C>\w{11})(?<K>\d{2})/')
                ],
                'attr' => array(
                    'placeholder' => '1234512345az12345678912'
                )
            ])
            ->add('bic', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Entrer un bic']),
                    new Regex('/^[a-z]{6}[0-9a-z]{2}([0-9a-z]{3})?\z/i')
                ],
                'attr' => array(
                    'placeholder' => 'NOLADE21STS'
                )
            ])
            ->add('address', AddressType::class)

            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save w-full px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-gray-700 rounded-md hover:bg-gray-600 focus:outline-none focus:bg-gray-600 mt-4'],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes.',
                    ]),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
