<?php

namespace App\Controller;
use App\Entity\Session;
use App\Entity\Grade;
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
    public function index(Request $request, InternshipRepository $internshipRepository): Response
{
    $user = $this->getUser();
    if ($user && !$user->getIsVerified()) {
        $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
    }

    $roles = $user->getRoles();
    $queryBuilder = $internshipRepository->createQueryBuilder('i')
        ->where('i.IsVerified = true');

    $em = $internshipRepository->getEntityManager();

    // Récupérer les paramètres de recherche
    $searchName = $request->query->get('search_name');
    $searchClass = $request->query->get('search_class');
    $searchSession = $request->query->get('search_session');

    $needJoinUser = $searchName || $searchClass || in_array('ROLE_TEACHER', $roles);

    if ($needJoinUser) {
        $queryBuilder->join('i.relation', 'u');
    }

    if ($searchName) {
        $queryBuilder
            ->andWhere('LOWER(u.firstName) LIKE :searchName OR LOWER(u.lastName) LIKE :searchName')
            ->setParameter('searchName', '%' . strtolower($searchName) . '%');
    }

    if ($searchClass) {
        $queryBuilder
            ->join('u.grade', 'g_search')
            ->andWhere('g_search.id = :searchClass')
            ->setParameter('searchClass', $searchClass);
    }

    if ($searchSession) {
        $queryBuilder
            ->andWhere('i.session = :searchSession')
            ->setParameter('searchSession', $searchSession);
    }

    if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPER_ADMIN', $roles)) {
        $internships = $queryBuilder->getQuery()->getResult();
        $grades = $em->getRepository(Grade::class)->findAll();
    } elseif (in_array('ROLE_TEACHER', $roles)) {
        $gradesCollection = $user->getGrade();
        $grades = $gradesCollection->toArray();

        $queryBuilder
            ->join('u.grade', 'g')
            ->andWhere('g IN (:grades)')
            ->setParameter('grades', $grades);

        $internships = $queryBuilder->getQuery()->getResult();
    } elseif (in_array('ROLE_STUDENT', $roles)) {
        $internships = $internshipRepository->findBy([
            'relation' => $user,
            'IsVerified' => true
        ]);
        $grades = [];
    } else {
        $internships = [];
        $grades = [];
    }

    $sessions = $em->getRepository(Session::class)->findAll();

    return $this->render('internship/index.html.twig', [
        'internships' => $internships,
        'sessions' => $sessions,
        'grades' => $grades,
    ]);
}

    

#[Route('/internship/validation', name: 'app_internship_validation', methods: ['GET'])]
public function validation(Request $request, InternshipRepository $internshipRepository): Response
{
    $user = $this->getUser();

    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour voir cette page.');
        return $this->redirectToRoute('app_login');
    }

    $roles = $user->getRoles();
    $queryBuilder = $internshipRepository->createQueryBuilder('i')
        ->where('i.IsVerified = false');

    $em = $internshipRepository->getEntityManager();

    // Recherche
    $searchName = $request->query->get('search_name');
    $searchClass = $request->query->get('search_class');
    $searchSession = $request->query->get('search_session');

    $needJoinUser = $searchName || $searchClass || in_array('ROLE_TEACHER', $roles);

    if ($needJoinUser) {
        $queryBuilder->join('i.relation', 'u');
    }

    if ($searchName) {
        $queryBuilder
            ->andWhere('LOWER(u.firstName) LIKE :searchName OR LOWER(u.lastName) LIKE :searchName')
            ->setParameter('searchName', '%' . strtolower($searchName) . '%');
    }

    if ($searchClass) {
        $queryBuilder
            ->join('u.grade', 'g_search')
            ->andWhere('g_search.id = :searchClass')
            ->setParameter('searchClass', $searchClass);
    }

    if ($searchSession) {
        $queryBuilder
            ->andWhere('i.session = :searchSession')
            ->setParameter('searchSession', $searchSession);
    }

    if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPER_ADMIN', $roles)) {
        $internships = $queryBuilder->getQuery()->getResult();
        $grades = $em->getRepository(Grade::class)->findAll();
    } elseif (in_array('ROLE_TEACHER', $roles)) {
        $gradesCollection = $user->getGrade();
        $grades = $gradesCollection->toArray();

        $queryBuilder
            ->join('u.grade', 'g')
            ->andWhere('g IN (:grades)')
            ->setParameter('grades', $grades);

        $internships = $queryBuilder->getQuery()->getResult();
    } elseif (in_array('ROLE_STUDENT', $roles)) {
        $internships = $internshipRepository->findBy([
            'relation' => $user,
            'IsVerified' => false
        ]);
        $grades = [];
    } else {
        $internships = [];
        $grades = [];
    }

    $sessions = $em->getRepository(Session::class)->findAll();

    return $this->render('internship/validation.html.twig', [
        'internships' => $internships,
        'sessions' => $sessions,
        'grades' => $grades,
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
            $this->addFlash('success', message: 'Stage soumis, attente de validation.');
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
public function edit(Request $request, Internship $internship, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    // Vérifie si l'utilisateur est un étudiant et s'il est propriétaire du stage
    if (in_array('ROLE_STUDENT', $user->getRoles())) {
        if ($internship->getRelation() !== $user) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour modifier ce stage.');
            return $this->redirectToRoute('app_internship_index');
        }

        if ($internship->IsVerified() !== false && $internship->IsVerified() !== null) {
            $this->addFlash('error', 'Vous ne pouvez modifier ce stage que s\'il n\'est pas encore validé.');
            return $this->redirectToRoute('app_internship_index');
        }
    }

    // Vérifie si l'utilisateur a le rôle enseignant
    if (!$this->isGranted('ROLE_TEACHER') && $internship->getRelation() !== $user) {
        $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour modifier ce stage.');
        return $this->redirectToRoute('app_internship_index');
    }

    $form = $this->createForm(InternshipType::class, $internship);
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
