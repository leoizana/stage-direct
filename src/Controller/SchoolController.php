<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Entity\School;
use App\Form\SchoolType;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/school')]
final class SchoolController extends AbstractController
{
    #[Route(name: 'app_school_index', methods: ['GET'])]
    public function index(SchoolRepository $schoolRepository): Response
    {
        return $this->render('school/index.html.twig', [
            'schools' => $schoolRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_school_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
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
    ]);
}

    
    
    

    #[Route('/{id}', name: 'app_school_show', methods: ['GET'])]
    public function show(School $school): Response
    {
        return $this->render('school/show.html.twig', [
            'school' => $school,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_school_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, School $school, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // After editing, flush changes to the database
            $entityManager->flush();

            return $this->redirectToRoute('app_school_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('school/edit.html.twig', [
            'school' => $school,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_school_delete', methods: ['POST'])]
    public function delete(Request $request, School $school, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$school->getId(), $request->get('_token'))) {
            $entityManager->remove($school);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_index', [], Response::HTTP_SEE_OTHER);
    }
}
