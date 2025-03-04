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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Security $security): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }


        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/inscription', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function inscription(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, EmailService $emailService): Response
    {
        $user = new User();

        // Vérifier si l'utilisateur est administrateur
        $loginType = 'user'; // Valeur par défaut
        if ($this->isGranted('ROLE_ADMIN')) {
            $loginType = 'admin'; // Si l'utilisateur est admin, il peut choisir un rôle
        }

        // Créer le formulaire avec le bon type (admin ou user)
        $form = $this->createForm(UserType::class, $user, [
            'login_type' => $loginType // Passer 'admin' ou 'user' en fonction des droits de l'utilisateur
        ]);

        // Gestion de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()  // Récupération du mot de passe depuis le formulaire
            );
            $user->setPassword($hashedPassword);

            // Ajout d'un rôle par défaut si nécessaire
            if (empty($user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
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
                $this->addFlash('error', 'Une erreur est survenue.');
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
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

}