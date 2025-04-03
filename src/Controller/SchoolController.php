<?php

namespace App\Controller;

use App\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\School;
use App\Form\SchoolType;
use App\Entity\Grade;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/school')]
final class SchoolController extends AbstractController
{
    #[Route(name: 'app_school_index', methods: ['GET'])]
    public function index(SchoolRepository $schoolRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        return $this->render('school/index.html.twig', [
            'schools' => $schoolRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_school_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        // Création d'une nouvelle instance de l'entité School
        $school = new School();

        // Création du formulaire en liant le formulaire à l'entité School
        $form = $this->createForm(SchoolType::class, $school);

        // Traitement de la requête pour vérifier si le formulaire a été soumis
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des nouvelles classes ajoutées dynamiquement
            $newGrades = $form->get('newGrade')->getData();
            dump($newGrades);

            // Vérification et ajout des nouvelles classes
            if (is_array($newGrades)) {
                foreach ($newGrades as $newGradeName) {
                    if ($newGradeName) {
                        $existingGrade = $entityManager->getRepository(Grade::class)->findOneBy(['className' => $newGradeName]);

                        // Si la classe n'existe pas, on la crée
                        if (!$existingGrade) {
                            $newGrade = new Grade();
                            $newGrade->setClassName($newGradeName);
                            $entityManager->persist($newGrade);
                            $school->addGrade($newGrade);
                        } else {
                            // Sinon, on associe la classe existante à l'école
                            $school->addGrade($existingGrade);
                        }
                    }
                }
            }

            // Enregistrement de l'école et des relations
            $entityManager->persist($school);
            $entityManager->flush();

            // Redirection après soumission réussie
            return $this->redirectToRoute('app_school_index');
        }

        // Si le formulaire n'est pas encore soumis ou invalide, on le passe à la vue
        return $this->render('school/new.html.twig', [
            'form' => $form->createView(), // Passe la vue du formulaire à Twig
            'sessions' => $entityManager->getRepository(Session::class)->findAll(), 
        ]);
    }

    #[Route('/{id}', name: 'app_school_show', methods: ['GET'])]
    public function show(School $school): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        return $this->render('school/show.html.twig', [
            'school' => $school,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_school_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, School $school, EntityManagerInterface $entityManager): Response
    {
        // Vérification des droits d'accès
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        // Création du formulaire
        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // 1️⃣ Récupération des nouvelles classes ajoutées dynamiquement
            $newGrades = $form->get('newGrade')->getData();
            dump($newGrades);
    
            // 2️⃣ Vérification et ajout des nouvelles classes
            if (is_array($newGrades)) {
                foreach ($newGrades as $newGradeName) {
                    if ($newGradeName) {
                        $existingGrade = $entityManager->getRepository(Grade::class)->findOneBy(['className' => $newGradeName]);
    
                        // Si la classe n'existe pas, on la crée
                        if (!$existingGrade) {
                            $newGrade = new Grade();
                            $newGrade->setClassName($newGradeName);
                            $entityManager->persist($newGrade);
                            $school->addGrade($newGrade);
                        } else {
                            // Sinon, on associe la classe existante à l'école
                            if (!$school->getGrades()->contains($existingGrade)) {
                                $school->addGrade($existingGrade);
                            }
                        }
                    }
                }
            }
    
            // 3️⃣ Récupération des nouvelles sessions ajoutées dynamiquement
            $newSessions = $form->get('newSession')->getData();
            dump($newSessions);
    
            if ($newSessions) {
                // On les sépare par virgule si l'utilisateur a ajouté plusieurs sessions
                $sessionNames = explode(',', $newSessions);
                foreach ($sessionNames as $sessionName) {
                    $sessionName = trim($sessionName); // Enlever les espaces superflus
                    if ($sessionName) {
                        $existingSession = $entityManager->getRepository(Session::class)->findOneBy(['sessionList' => $sessionName]);
            
                        // Si la session n'existe pas, on la crée
                        if (!$existingSession) {
                            $newSession = new Session();
                            $newSession->setSessionList($sessionName);
                            $entityManager->persist($newSession);
                            $school->addSession($newSession);
                        } else {
                            // Sinon, on associe la session existante à l'école
                            if (!$school->getSessions()->contains($existingSession)) {
                                $school->addSession($existingSession);
                            }
                        }
                    }
                }
            }
            
    
            // 5️⃣ Enregistrement des modifications en base de données
            $entityManager->flush();
    
            // Redirection après modification
            return $this->redirectToRoute('app_school_index');
        }
    
        // Retourner le formulaire avec la vue, et ajouter la liste des sessions
        return $this->render('school/edit.html.twig', [
            'school' => $school,
            'form' => $form->createView(),
            'sessions' => $school->getSessions(), 
        ]);
    }

    #[Route('/{id}', name: 'app_school_delete', methods: ['POST'])]
    public function delete(Request $request, School $school, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        if ($this->isCsrfTokenValid('delete' . $school->getId(), $request->get('_token'))) {
            $entityManager->remove($school);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_index', [], Response::HTTP_SEE_OTHER);
    }
}
