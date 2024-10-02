<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lastname')
            ->add('email')
            //->add('roles')
            ->add('roles', ChoiceType::class, [

                    'multiple' => true,
                    'expanded' => true,
                    'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Etudiant' => 'ROLE_STUDENT',
                    'Professeur' => 'ROLE_TEACHER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Super administrateur' => 'ROLE_SUPER_ADMIN',


                ],
            ])


        ;
    }
}