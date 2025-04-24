<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\EmailService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index');
        }
    
        $roles = $user->getRoles();
    
        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            $users = $userRepository->createQueryBuilder('u')
                ->where('u.isApprovedByTeacher = true')
                ->getQuery()
                ->getResult();
        } elseif (in_array('ROLE_ADMIN', $roles)) {
            $users = $userRepository->createQueryBuilder('u')
                ->where('u.roles NOT LIKE :role')
                ->andWhere('u.isApprovedByTeacher = true')
                ->setParameter('role', '%ROLE_SUPER_ADMIN%')
                ->getQuery()
                ->getResult();
        } elseif (in_array('ROLE_TEACHER', $roles)) {
            $teacherGrades = $user->getGrade();
    
            $users = $userRepository->createQueryBuilder('u')
                ->innerJoin('u.grade', 'g')
                ->where('g IN (:grades)')
                ->andWhere('u.isApprovedByTeacher = true')
                ->setParameter('grades', $teacherGrades)
                ->getQuery()
                ->getResult();
        } else {
            $users = [];
        }
    
        $roleHierarchy = ['ROLE_USER', 'ROLE_STUDENT', 'ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
        foreach ($users as $user) {
            $userRoles = $user->getRoles();
            usort($userRoles, fn($a, $b) => array_search($b, $roleHierarchy) <=> array_search($a, $roleHierarchy));
            $user->highestRole = $userRoles[0] ?? 'ROLE_USER';
        }
    
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }
    
    
    #[Route('/validation', name: 'app_user_validation', methods: ['GET'])]
public function validation(UserRepository $userRepository): Response
{
    // Vérification des permissions pour accéder à cette page
    if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_TEACHER')) {
        $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
        return $this->redirectToRoute('app_index');
    }

    // Si l'utilisateur est un admin ou super-admin, ils voient tous les utilisateurs non validés
    if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPER_ADMIN')) {
        $users = $userRepository->findBy(['isApprovedByTeacher' => false]);
    }
    // Si l'utilisateur est un professeur, il voit seulement ses élèves non validés
    else if ($this->isGranted('ROLE_TEACHER')) {
        $currentUser = $this->getUser();

        // Récupérer tous les utilisateurs ayant une classe assignée au professeur
        // Supposons que tu as un champ `grade` dans l'utilisateur qui représente ses classes.
        $users = $userRepository->createQueryBuilder('u')
            ->join('u.grade', 'g')
            ->where('u.isApprovedByTeacher = false')
            ->andWhere('g IN (:grades)')
            ->setParameter('grades', $currentUser->getGrade())
            ->getQuery()
            ->getResult();
    }

    // Trier les utilisateurs par leur rôle
    $roleHierarchy = ['ROLE_USER', 'ROLE_STUDENT', 'ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
    foreach ($users as $user) {
        $userRoles = $user->getRoles();

        // Trier les rôles selon la hiérarchie et récupérer le plus élevé
        usort($userRoles, function ($a, $b) use ($roleHierarchy) {
            return array_search($b, $roleHierarchy) <=> array_search($a, $roleHierarchy);
        });

        $user->highestRole = $userRoles[0] ?? 'ROLE_USER'; // Par défaut, on met "ROLE_USER" si vide
    }

    return $this->render('user/validation.html.twig', [
        'users' => $users, // Afficher les utilisateurs non validés
    ]);
}
    
    
    
    #[Route('/inscription', name: 'app_user_new', methods: ['GET', 'POST'])]
public function inscription(
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher,
    EmailService $emailService
): Response {
    $user = new User();

    // Vérifier si l'utilisateur qui crée le compte est administrateur
    $loginType = 'user'; // Valeur par défaut
    $isAdminCreating = false;
    if ($this->isGranted('ROLE_ADMIN')) {
        $loginType = 'admin'; // Si l'utilisateur est admin, il peut choisir un rôle
        $isAdminCreating = true; // Flag pour indiquer que l'admin crée le compte
    }

    // Créer le formulaire avec le bon type (admin ou user)
    $form = $this->createForm(UserType::class, $user, [
        'login_type' => $loginType, // Passer 'admin' ou 'user' en fonction des droits de l'utilisateur
    ]);

    // Gestion de la soumission du formulaire
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le mot de passe depuis le formulaire directement
        $plainPassword = $form->get('password')->getData();

        // Hachage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);

        // Ajout d'un rôle par défaut si nécessaire
        if (empty($user->getRoles())) {
            $user->setRoles(['ROLE_USER']); // Rôle par défaut
        }

        // Si l'utilisateur qui crée le compte est un admin, approuver directement l'utilisateur
        if ($isAdminCreating) {
            $user->setIsApprovedByTeacher(true); // Validation automatique par un administrateur
        } else {
            // Sinon, il est créé comme non approuvé
            $user->setIsApprovedByTeacher(false); // Définir sur false pour les autres cas
        }

        // Génération du token de vérification
        $verificationToken = bin2hex(random_bytes(16));
        $user->setVerificationToken($verificationToken);

        // Persist et flush
        $entityManager->persist($user);
        $entityManager->flush();

        // Envoyer l'email de confirmation avec le lien de vérification
        $emailSubject = "Confirmation de votre inscription";
        $emailText = "Bonjour " . $user->getFirstName() . ",\n\nVeuillez confirmer votre inscription en cliquant sur ce lien :\n\n" .
            $this->generateUrl('app_user_verify_email', ['token' => $verificationToken], 0);
        $destinataire = $user->getEmail();
        $result = $emailService->sendEmail($emailSubject, $emailText, $destinataire);

        if ($result == "00") {
            // Succès
            $this->addFlash('success', 'Votre inscription a été réussie. Un email de confirmation vous a été envoyé.');
        } else {
            // Échec
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'email.');
        }

        return $this->redirectToRoute('app_login');
    }

    return $this->render('user/new.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
    ]);
}

    #[Route('/verifier-email/{token}', name: 'app_user_verify_email')]
    public function verifyEmail(string $token, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // Rechercher l'utilisateur en fonction du token
        $user = $userRepository->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Le lien de vérification est invalide.');
            return $this->redirectToRoute('app_home');
        }

        // Si l'utilisateur est trouvé, marquer comme vérifié
        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été vérifié avec succès !');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        Security $security
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le mot de passe a été modifié, on le hache avant de persister
            $newPassword = $form->get('password')->getData(); // ✅ On récupère directement le champ du formulaire

            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();


            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, Security $security): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }

        // Vérification du token CSRF pour éviter les attaques
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, Security $security): Response
    {
        if (!$this->isGranted('ROLE_TEACHER')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index');
        }
    
        // Définition de la hiérarchie des rôles
        $roleHierarchy = ['ROLE_USER', 'ROLE_STUDENT', 'ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
    
        $userRoles = $user->getRoles();
    
        // Trier les rôles et récupérer le plus élevé
        usort($userRoles, function ($a, $b) use ($roleHierarchy) {
            return array_search($b, $roleHierarchy) <=> array_search($a, $roleHierarchy);
        });
    
        $highestRole = $userRoles[0] ?? 'ROLE_USER'; // Par défaut, "ROLE_USER" si vide
    
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'highestRole' => $highestRole, // Utilisation du rôle calculé
        ]);
    }
    #[Route('/user/{id}/approve', name: 'app_user_approve', methods: ['POST'])]
public function approveUser(User $user, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_TEACHER');
    $user->setIsApprovedByTeacher(true);
    $em->flush();

    $this->addFlash('success', 'Utilisateur validé avec succès.');
    return $this->redirectToRoute('app_user_validation');
}
#[Route('/user/{id}/reject', name: 'app_user_reject', methods: ['POST'])]
public function rejectUser(User $user, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_TEACHER');
    $em->remove($user);
    $em->flush();

    $this->addFlash('success', 'Utilisateur refusé et supprimé.');
    return $this->redirectToRoute('app_user_validation');
}
}