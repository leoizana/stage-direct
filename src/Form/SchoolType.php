<?php

// src/Form/SchoolType.php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\School;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('sessions', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'session_list',
                'multiple' => true,  // Vous pouvez permettre la sélection multiple si nécessaire
                'expanded' => true,  // Par exemple, pour des cases à cocher
                'required' => false,
                'mapped' => true,    // Lier cette propriété à l'entité School
            ])
            ->add('newSessions', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true, 
                'by_reference' => false, 
                'required' => false, 
            ])
            ->add('grades', EntityType::class, [
                'class' => Grade::class,
                'choice_label' => 'className',
                'multiple' => true,
                'expanded' => true, // Pour afficher les cases à cocher
            ])
            // Utilisation de CollectionType pour permettre l'ajout de plusieurs nouvelles classes
            ->add('newGrades', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'mapped' => false, // Ce champ n'est pas persisté directement dans l'entité
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => School::class,
        ]);
    }
}
