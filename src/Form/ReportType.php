<?php
namespace App\Form;

use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du rapport',  // Label en français
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom de l\'étudiant',  // Label en français
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom de l\'étudiant',  // Label en français
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu du rapport',  // Label en français
            ])
            ->add('professorEmail', EmailType::class, [
                'label' => 'Email du professeur',  // Label en français
            ])
            ->add('session', TextType::class, [
                'label' => 'Session (ex : 2024-2025)',
            ])
            ->add('classe', TextType::class, [
                'label' => 'Classe de l\'élève',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}
