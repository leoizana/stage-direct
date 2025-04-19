<?php

namespace App\Controller;

use App\Entity\Internship;
use App\Form\InternshipType;
use App\Repository\InternshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stage')]
final class InternshipController extends AbstractController
{
    #[Route('/', name: 'app_internship_index', methods: ['GET'])]
public function index(InternshipRepository $internshipRepository): Response
{
    $user = $this->getUser();

    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour voir cette page.');
        return $this->redirectToRoute('app_login');
    }

    $roles = $user->getRoles();
    $queryBuilder = $internshipRepository->createQueryBuilder('i')
        ->where('i.IsVerified = true');

    if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPER_ADMIN', $roles)) {
        // Admins voient tout ce qui est validé
        $internships = $queryBuilder->getQuery()->getResult();

    } elseif (in_array('ROLE_TEACHER', $roles)) {
        // Profs : stages des élèves de leur classe
        $classe = $user->getGrade();
        $queryBuilder
            ->join('i.relation', 'u')
            ->join('u.grade', 'g')
            ->andWhere('g = :grade')
            ->setParameter('grade', $classe);

        $internships = $queryBuilder->getQuery()->getResult();

    } elseif (in_array('ROLE_STUDENT', $roles)) {
        // Élèves : seulement leur propre stage
        $internships = $internshipRepository->findBy([
            'relation' => $user,
            'IsVerified' => true
        ]);
    } else {
        $internships = [];
    }

    return $this->render('internship/index.html.twig', [
        'internships' => $internships,
    ]);
}

    #[Route('/internship/validation', name: 'app_internship_validation', methods: ['GET'])]
public function validation(InternshipRepository $internshipRepository): Response
{
    $validationInternships = $internshipRepository->findBy(['IsVerified' => false]);

    return $this->render('internship/validation.html.twig', [
        'internships' => $validationInternships,
    ]);
}

    #[Route('/new', name: 'app_internship_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour créer un stage.');
            return $this->redirectToRoute('app_login');
        }
    
        $internship = new Internship();
        $internship->setVerified(false); 
        $internship->setRelation($user); // Associe l'utilisateur connecté
        $form = $this->createForm(InternshipType::class, $internship);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($internship);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_internship_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('internship/new.html.twig', [
            'internship' => $internship,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_internship_show', methods: ['GET'])]
    public function show(internship $internship): Response
    {   
        return $this->render('internship/show.html.twig', [
            'internship' => $internship,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_internship_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, internship $internship, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        $form = $this->createForm(internshipType::class, $internship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_internship_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('internship/edit.html.twig', [
            'internship' => $internship,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_internship_delete', methods: ['POST'])]
    public function delete(Request $request, Internship $internship, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        if ($this->isCsrfTokenValid('delete' . $internship->getId(), $request->request->get('_token'))) {
            $entityManager->remove($internship);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_internship_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/internship/{id}/approve', name: 'app_internship_approve', methods: ['POST'])]
public function approve(Internship $internship, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_TEACHER');
    $internship->setVerified(true);
    $em->flush();

    $this->addFlash('success', message: 'Stage accepté et validé.');
    return $this->redirectToRoute('app_internship_validation');
}

#[Route('/internship/{id}/reject', name: 'app_internship_reject', methods: ['POST'])]
public function reject(Internship $internship, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted(attribute: 'ROLE_TEACHER');
    $em->remove($internship);
    $em->flush();

    $this->addFlash('success', 'Stage refusé et supprimé.');
    return $this->redirectToRoute('app_internship_validation');
}

    
}
