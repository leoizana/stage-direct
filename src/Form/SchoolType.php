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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
            // Utilisation de CollectionType pour permettre l'ajout de plusieurs nouvelles classes
            ->add('newGrade', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'mapped' => false, // Ce champ n'est pas persisté directement dans l'entité
                'required' => false,
                'label' => 'Nouvelles Classes',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => School::class,
        ]);
    }
}
