<?php

// src/Form/UserType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Grade;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;  // Importation du EmailType
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserType extends AbstractType
{
    private $authorizationChecker;

    // Injection du service AuthorizationCheckerInterface dans le constructeur
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;

    }
    private function addPasswordField(FormBuilderInterface $builder, bool $required = true): void
{
    $builder->add('password', PasswordType::class, [
        'label' => 'Mot de passe',
        'mapped' => false,
        'required' => $required,
        'constraints' => [
            new Assert\NotBlank(['message' => 'Veuillez saisir un mot de passe.']),
            new Assert\Length([
                'min' => 12,
                'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
            ]),
            new Assert\Regex([
                'pattern' => '/[A-Z]/',
                'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.',
            ]),
            new Assert\Regex([
                'pattern' => '/[a-z]/',
                'message' => 'Le mot de passe doit contenir au moins une lettre minuscule.',
            ]),
            new Assert\Regex([
                'pattern' => '/\d/',
                'message' => 'Le mot de passe doit contenir au moins un chiffre.',
            ]),
            new Assert\Regex([
                'pattern' => '/[\W_]/',
                'message' => 'Le mot de passe doit contenir au moins un caractère spécial.',
            ]),
        ],
        'attr' => [
            'autocomplete' => 'new-password',
            'placeholder' => '••••••••',
            'class' => 'bg-gray-50 border border-gray-300 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
        ],
    ]);
}


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit']; // Vérifie si on est en édition
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'text-white text-sm font-medium mb-4',
                ],
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => 'Prénom',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'text-white text-sm font-medium mb-4',
                ],
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'Date de naissance',
                'label_attr' => [
                    'class' => 'text-white text-sm font-medium mb-4',
                ],
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'class' => 'phone-input',
                    'placeholder' => 'Numéro de téléphone'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse postale',
                'label_attr' => [
                    'class' => 'text-white text-sm font-medium mb-4',
                ],
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => 'Adresse',
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code Postal',
                'label_attr' => [
                    'class' => 'text-white text-sm font-medium mb-4',
                ],
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => 'Code postal',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'label_attr' => [
                    'class' => 'text-white text-sm font-medium mb-4',
                ],
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => 'Ville',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Mail',
                'label_attr' => [
                    'class' => 'text-white text-sm font-medium mb-4',
                ],
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => 'nom@mail.fr',
                ],
            ]);
        // Ajout du champ grade
        $builder->add('grade', EntityType::class, [
            'class' => Grade::class,
            'choice_label' => 'className',
            'multiple' => true,
            'expanded' => false, // true si tu veux des cases à cocher
            'label' => 'Classe',
            'label_attr' => ['class' => 'text-white text-sm font-medium mb-4'],
            'attr' => [
                'class' => 'bg-gray-50 border border-gray-300 text-white text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
            ],
            'query_builder' => function (\App\Repository\GradeRepository $repo) {
                return $repo->createQueryBuilder('g')->orderBy('g.className', 'ASC');
            },
        ]);

        if ($options['is_edit']) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false, // Symfony ne va pas lier ce champ à l'entité User
                'required' => false, // L'utilisateur n'est pas obligé de le remplir en mode édition
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => '••••••••',
                ],
            ]);
        } else {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de Passe',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le mot de passe est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/\d/',
                        'message' => 'Votre mot de passe doit contenir au moins un chiffre.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[\W_]/',
                        'message' => 'Votre mot de passe doit contenir au moins un caractère spécial.',
                    ]),
                ],
                'attr' => [
                    'class' => 'bg-gray-50 border border-gray-300 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                    'placeholder' => '••••••••',
                ],
            ]);
        }


        // Vérification si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Étudiant' => 'ROLE_STUDENT',
                    'Professeur' => 'ROLE_TEACHER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Super-Administrateur' => 'ROLE_SUPER_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => 'Rôles',
                'label_attr' => ['class' => 'text-white text-sm font-medium mb-4'],
                'attr' => ['class' => 'flex flex-col gap-2'],
                'data' => array_values($options['data']->getRoles()), // S'assure que les rôles sont sous forme d'un simple tableau
            ]);

        } else {
            // Vérification si l'utilisateur a le rôle ROLE_ADMIN
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $builder->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Utilisateur' => 'ROLE_USER',
                        'Étudiant' => 'ROLE_STUDENT',
                        'Professeur' => 'ROLE_TEACHER',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'Rôles',
                    'label_attr' => ['class' => 'text-white text-sm font-medium mb-4'],
                    'attr' => ['class' => 'flex flex-col gap-2'],
                    'data' => array_values($options['data']->getRoles()), // S'assure que les rôles sont sous forme d'un simple tableau
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false, // Définit la valeur par défaut
        ]);

        $resolver->setDefined(['is_edit', 'login_type']);
    }
}