<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('email', EmailType::class, [
            'label' => 'Adresse Mail',
            'label_attr' => [
                'class' => 'text-white text-sm font-medium mb-4', // Ajout de classes pour la police
            ],
            'attr' => [
                'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                'placeholder' => 'nom@mail.fr',
            ],
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Mot de Passe',
            'label_attr' => [
                'class' => 'text-white text-sm font-medium mb-4', // Ajout de classes pour la police
            ],
            'attr' => [
                'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                'placeholder' => '••••••••',
            ],
        ])
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Étudiant' => 'ROLE_STUDENT',
                'Professeur' => 'ROLE_TEACHER',
                'Administrateur' => 'ROLE_ADMIN',
                'Super-Administrateur' => 'ROLE_SUPER_ADMIN',
            ],
            'multiple' => true, // Permet de sélectionner plusieurs rôles
            'expanded' => true, // Affiche sous forme de cases à cocher
            'label' => 'Rôles',
            'label_attr' => [
                'class' => 'text-white text-sm font-medium mb-4', 
            ],
            'attr' => [
                'class' => 'flex flex-col gap-2', 
            ],
        ])
        ;
}


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'login_type' => '', // Définir la valeur par défaut
        ]);
    }
}