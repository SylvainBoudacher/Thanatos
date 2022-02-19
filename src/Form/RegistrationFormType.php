<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('birthDate', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('address' , TextType::class)
            ->add('email')

            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Le mot de passe doit être le même.',
                    'options' => ['attr' => ['class' => 'password-field block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-300 focus:ring-opacity-40 dark:focus:border-blue-300 focus:outline-none focus:ring']],
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe', 'label_attr' => ['class' => 'text-gray-700 dark:text-gray-200 mt-4']],
                    // Repeat password -> duplique l'input pour la vérification du password
                    'second_options' => ['label' => 'Comfirmation mot de passe', 'label_attr' => ['class' => 'text-gray-700 dark:text-gray-200 mt-4']],
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'label_attr' => ['class' => 'test'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit faire au minimum {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 100,
                        ]),
                    ],
                ]
            )

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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
