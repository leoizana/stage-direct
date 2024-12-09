<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\Student;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;  // Ajout du EmailType pour l'email
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface;

class StudentType extends AbstractType
{
    private $entityManager;

    // Injection de l'EntityManager pour accéder à la base de données
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('className')
            ->add('school', EntityType::class, [
                'class' => School::class,
                'choice_label' => 'address',  // Affichage de l'adresse pour l'établissement
                'placeholder' => 'Sélectionnez une école',
                'attr' => ['class' => 'school-select'],
            ])
            ->add('user', EmailType::class, [
                'label' => 'Email de l\'utilisateur',
                'attr' => [
                    'class' => 'email-input',
                    'placeholder' => 'Email de l\'utilisateur (doit être unique)',
                ],
                'constraints' => [
                    // Vous pouvez ajouter des contraintes de validation ici si nécessaire
                ],
            ])
            ->add('className', ChoiceType::class, [
                'choices' => $this->getClassChoices(),
                'label' => 'Sélectionner la classe',
                'attr' => [
                    'class' => 'class-select',
                    'placeholder' => 'Choisissez la classe selon l\'école'
                ],
            ])
        ;

        // Écoute des changements sur le champ "school" pour actualiser dynamiquement les classes
        $builder->get('school')->addEventListener(
            \Symfony\Component\Form\FormEvents::POST_SUBMIT,
            function ($event) use ($builder) {
                $school = $event->getData();
                $classChoices = $this->getClassChoicesForSchool($school);
                $builder->get('className')->resetViewTransformers();
                $builder->get('className')->setChoices($classChoices);
            }
        );
    }

    // Fonction pour récupérer les classes disponibles (à adapter selon votre logique)
    private function getClassChoicesForSchool(School $school): array
    {
        // Supposons qu'il y ait une relation entre School et Class (Grade ou autre)
        $classes = $school->getClasses(); // Récupérer les classes de l'école

        $choices = [];
        foreach ($classes as $class) {
            $choices[$class->getName()] = $class->getId(); // Remplacer 'Name' par le nom réel de la classe
        }
        return $choices;
    }

    // Fonction pour obtenir les choix de classes disponibles
    private function getClassChoices(): array
    {
        return [];  // Retourne un tableau vide si aucune classe n'est sélectionnée
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
