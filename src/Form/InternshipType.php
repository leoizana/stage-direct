<?php

namespace App\Form;

use App\Entity\Internship;
use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Session;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;  // Utilisation de DateType au lieu de DateTimeType

class InternshipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',  // Affiche un champ de date simple sans heure
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',  // Affiche un champ de date simple sans heure
            ])
            ->add('session', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'session_list', 
                'label' => 'Session',
                'placeholder' => 'Choisir une session',
                'attr' => ['class' => 'px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none'],
            ])
            ->add('themes', TextareaType::class, [
                'label' => 'Rapports',
                'required' => false,
            ])
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => function (Company $company) {
                    return $company->getName() . ' (' . $company->getCity() . ')';
                },
                'label' => 'Entreprise',
                'placeholder' => 'Choisir une entreprise',
                'query_builder' => function (\App\Repository\CompanyRepository $repo) {
                    return $repo->createQueryBuilder('c')
                                ->orderBy('c.name', 'ASC');
                },
                'attr' => [
                    'class' => 'px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none',
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Internship::class,
        ]);
    }
}
