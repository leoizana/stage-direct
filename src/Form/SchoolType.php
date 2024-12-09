<?php

// src/Form/SchoolType.php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\School;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SchoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('zipcode')
            ->add('city')
            ->add('phone')
            ->add('email')
            ->add('grades', EntityType::class, [
                'class' => Grade::class,
                'choice_label' => 'className',
                'multiple' => true,
                'expanded' => true, // Pour afficher les cases à cocher
            ])
            // Champ pour saisir une nouvelle classe
            ->add('newGrade', TextType::class, [
                'required' => false,
                'label' => 'Ajouter une nouvelle classe',
                'attr' => ['placeholder' => 'Nom de la classe'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => School::class,
        ]);
    }
}
