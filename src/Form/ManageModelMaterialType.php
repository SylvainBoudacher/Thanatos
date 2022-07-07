<?php

namespace App\Form;

use App\Entity\CompanyMaterial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ManageModelMaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('materials', EntityType::class, [
                'class' => CompanyMaterial::class,
                'multiple' => true,
                'expanded' => true,
            ]);
    }

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => ModelMaterial::class,
//        ]);
//    }
}
