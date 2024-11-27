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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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

            // Persist et flush
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer l'email de confirmation
            $emailSubject = "Confirmation de " . $user->getEmail();
            $emailText = "Bonjour " . $user->getFirstName();
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


    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        Security $security
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le mot de passe a été modifié, on le hache avant de persister
            if ($user->getPassword()) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($hashedPassword);
            }

            // Sauvegarder les changements
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
        
}
