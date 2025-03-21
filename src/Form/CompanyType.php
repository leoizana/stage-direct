<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Activity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('street')              
            ->add('zipCode')
            ->add('city')
            ->add('country')
            ->add('phone')
            ->add('email')
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'label', // Utilisation du label de l'Activity
                'placeholder' => 'Choisissez une activité',
                'required' => false,
                'attr' => ['class' => 'px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none'],
                'label_attr' => ['class' => 'mb-2 text-sm font-medium text-gray-700'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
