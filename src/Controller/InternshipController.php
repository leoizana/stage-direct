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
use App\Service\EmailService;
use App\Repository\UserRepository;


#[Route('/stage')]
final class InternshipController extends AbstractController
{
    
    #[Route('/', name: 'app_internship_index', methods: ['GET'])]
    public function index(Request $request, InternshipRepository $internshipRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir cette page.');
            return $this->redirectToRoute('app_login');
        }

        if ($user && !$user->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
        }
    
        $roles = $user->getRoles();
        $limit = 15;
        $page = max(1, (int) $request->query->get('page', 1));
        $offset = ($page - 1) * $limit;
        
        $grades = $em->getRepository(Grade::class)->findAll();
        $sessions = $em->getRepository(Session::class)->findAll();
        $searchName = $request->query->get('search_name');
        $searchClass = $request->query->get('search_class');
        $searchSession = $request->query->get('search_session');

        $queryBuilder = $internshipRepository->createQueryBuilder('i')
            ->where('i.IsVerified = true');

        // Restriction selon le rôle
        if (in_array('ROLE_STUDENT', $roles)) {
            // Un étudiant ne voit que ses stages
            $queryBuilder
                ->andWhere('i.relation = :userId')
                ->setParameter('userId', $user->getId());
        } elseif (in_array('ROLE_TEACHER', $roles)) {
            // Un professeur ne voit que les stages des étudiants de ses classes
            $queryBuilder
                ->join('i.relation', 'u')
                ->join('u.grade', 'g')
                ->andWhere('g IN (:teacherGrades)')
                ->setParameter('teacherGrades', $user->getGrade());
        }
        // Les admins voient tout (pas de restriction supplémentaire)

        // Reste du code de filtrage
        $needJoinUser = $searchName || $searchClass;
        
        if ($needJoinUser && !in_array('ROLE_TEACHER', $roles)) {
            $queryBuilder->join('i.relation', 'u');
        }

        if ($searchName) {
            $queryBuilder
                ->andWhere('LOWER(u.firstName) LIKE :searchName OR LOWER(u.lastName) LIKE :searchName')
                ->setParameter('searchName', '%' . strtolower($searchName) . '%');
        }

        if ($searchClass) {
            if (!$queryBuilder->getDQLPart('join')['u']) {
                $queryBuilder->join('i.relation', 'u');
            }
            if (!$queryBuilder->getDQLPart('join')['g']) {
                $queryBuilder->join('u.grade', 'g');
            }
            $queryBuilder
                ->andWhere('g.id = :searchClass')
                ->setParameter('searchClass', $searchClass);
        }

        if ($searchSession) {
            $queryBuilder
                ->andWhere('i.session = :searchSession')
                ->setParameter('searchSession', $searchSession);
        }
    
        // Clone pour compter
        $countQueryBuilder = clone $queryBuilder;
        $result = $countQueryBuilder
            ->select('COUNT(i.id) as total')
            ->getQuery()
            ->getOneOrNullResult();
    
        $total = $result['total'] ?? 0;
        $maxPages = max(1, ceil($total / $limit));
    
        // Redirection si la page demandée est trop grande
        if ($page > $maxPages) {
            return $this->redirectToRoute('app_internship_index', [
                'page' => $maxPages,
                'search_name' => $searchName,
                'search_class' => $searchClass,
                'search_session' => $searchSession
            ]);
        }
    
        // Résultats paginés
        $internships = $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    
        return $this->render('internship/index.html.twig', [
            'internships' => $internships,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
            'pages' => $maxPages,
            'searchName' => $searchName,
            'searchClass' => $searchClass,
            'searchSession' => $searchSession,
            'grades' => $grades,
            'sessions' => $sessions,
        ]);
    }
    


    

    #[Route('/internship/validation', name: 'app_internship_validation', methods: ['GET'])]
public function validation(Request $request, InternshipRepository $internshipRepository, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour voir cette page.');
        return $this->redirectToRoute('app_login');
    }

    $roles = $user->getRoles();
    $limit = 15;
    $page = max(1, (int) $request->query->get('page', 1));
    $offset = ($page - 1) * $limit;
    $grades = $em->getRepository(Grade::class)->findAll();
    $sessions = $em->getRepository(Session::class)->findAll();
    $searchName = $request->query->get('search_name');
    $searchClass = $request->query->get('search_class');
    $searchSession = $request->query->get('search_session');
    
    $queryBuilder = $internshipRepository->createQueryBuilder('i')
        ->where('i.IsVerified = false');

    // Restriction selon le rôle
    if (in_array('ROLE_STUDENT', $roles)) {
        // Un étudiant ne voit que ses stages
        $queryBuilder
            ->andWhere('i.relation = :userId')
            ->setParameter('userId', $user->getId());
    } elseif (in_array('ROLE_TEACHER', $roles) && !in_array('ROLE_ADMIN', $roles)) {
        // Un professeur ne voit que les stages des étudiants de ses classes
        $queryBuilder
            ->join('i.relation', 'u')
            ->join('u.grade', 'g')
            ->andWhere('g IN (:teacherGrades)')
            ->setParameter('teacherGrades', $user->getGrade());
    }
    // Les admins voient tout (pas de restriction supplémentaire)

    // Reste du code de filtrage
    $needJoinUser = $searchName || $searchClass;
    
    if ($needJoinUser && !$queryBuilder->getDQLPart('join')['u']) {
        $queryBuilder->join('i.relation', 'u');
    }

    if ($searchName) {
        $queryBuilder
            ->andWhere('LOWER(u.firstName) LIKE :searchName OR LOWER(u.lastName) LIKE :searchName')
            ->setParameter('searchName', '%' . strtolower($searchName) . '%');
    }

    if ($searchClass) {
        if (!$queryBuilder->getDQLPart('join')['u']) {
            $queryBuilder->join('i.relation', 'u');
        }
        if (!$queryBuilder->getDQLPart('join')['g']) {
            $queryBuilder->join('u.grade', 'g');
        }
        $queryBuilder
            ->andWhere('g.id = :searchClass')
            ->setParameter('searchClass', $searchClass);
    }

    if ($searchSession) {
        $queryBuilder
            ->andWhere('i.session = :searchSession')
            ->setParameter('searchSession', $searchSession);
    }
    
    // Clone pour compter
    $countQueryBuilder = clone $queryBuilder;
    $result = $countQueryBuilder
        ->select('COUNT(i.id) as total')
        ->getQuery()
        ->getOneOrNullResult();

    $total = $result['total'] ?? 0;
    $maxPages = max(1, ceil($total / $limit));

    // Redirection si la page demandée est trop grande
    if ($page > $maxPages) {
        return $this->redirectToRoute('app_internship_validation', [
            'page' => $maxPages,
            'search_name' => $searchName,
            'search_class' => $searchClass,
            'search_session' => $searchSession
        ]);
    }

    // Résultats paginés
    $internships = $queryBuilder
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

    $sessions = $em->getRepository(Session::class)->findAll();

    // Retourner les résultats avec la pagination et les filtres
    return $this->render('internship/validation.html.twig', [
        'internships' => $internships,
        'total' => $total,
        'limit' => $limit,
        'page' => $page,
        'pages' => $maxPages,
        'searchName' => $searchName,
        'searchClass' => $searchClass,
        'searchSession' => $searchSession,
        'sessions' => $sessions,
        'grades' => $grades,
    ]);
}
    

#[Route('/new', name: 'app_internship_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, EmailService $emailService): Response
{
    $user = $this->getUser();
    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour créer un stage.');
        return $this->redirectToRoute('app_login');
    }

    $internship = new Internship();
    $internship->setVerified(false);
    $internship->setRelation($user);

    $form = $this->createForm(InternshipType::class, $internship);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($internship);
        $entityManager->flush();

        $studentGrade = $user->getGrade();
        $teachers = $userRepository->createQueryBuilder('u')
            ->innerJoin('u.grade', 'g')
            ->where('g IN (:grades)')
            ->andWhere('u.WantMail = :wantMail')
            ->andWhere('u.isApprovedByTeacher = :approved')
            ->setParameter('grades', $studentGrade)
            ->setParameter('wantMail', true)
            ->setParameter('approved', true)
            ->getQuery()
            ->getResult();
    
        // Envoi d'email à chaque prof
        foreach ($teachers as $teacher) {
            $subject = "Nouveau stage soumis par " . $user->getFirstName() . " " . $user->getLastName();
            $message = "Bonjour " . $teacher->getFirstName() . ",\n\n" .
                       $user->getFirstName() . " " . $user->getLastName() . " a soumis un stage le " . (new \DateTime())->format('d/m/Y H:i') . ".\n\n" .
                       "Entreprise : " . $internship->getCompany()->getName() . "\n" . 
                       "Date de début : " . $internship->getDateDebut()->format('d/m/Y') . "\n" .
                       "Date de fin : " . $internship->getDateFin()->format('d/m/Y') . "\n\n" .
                       "Merci de valider ce stage via Stage-Direct.";

            $emailService->sendEmail($subject, $message, $teacher->getEmail());
        }

        $this->addFlash('success', 'Stage soumis, attente de validation.');
        return $this->redirectToRoute('app_internship_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('internship/new.html.twig', [
        'internship' => $internship,
        'form' => $form,
    ]);
}



    #[Route('/{id}', name: 'app_internship_show', methods: ['GET'])]
public function show(Internship $internship): Response
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


#[Route('/{id}/rapport', name: 'app_internship_report', methods: ['GET', 'POST'])]
public function teacherReport(Request $request, Internship $internship, EntityManagerInterface $entityManager): Response
{
    // Vérifier que l'utilisateur est un professeur
    $this->denyAccessUnlessGranted('ROLE_TEACHER');

    // Vérifier que le stage est validé
    if (!$internship->IsVerified()) {
        $this->addFlash('error', 'Le stage doit être validé pour pouvoir ajouter un rapport.');
        return $this->redirectToRoute('app_internship_index');
    }

    if ($request->isMethod('POST')) {
        $report = $request->request->get('report');
        $internship->setTeacherReport($report);
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Rapport enregistré avec succès.');
        return $this->redirectToRoute('app_internship_index');
    }

    return $this->render('internship/rapport_prof.html.twig', [
        'internship' => $internship
    ]);
}
#[Route('/export/verified', name: 'app_internship_export_verified', methods: ['GET'])]
public function exportVerified(Request $request, InternshipRepository $internshipRepository): Response
{
    // Récupérer les mêmes paramètres de recherche que dans l'index
    $searchName = $request->query->get('search_name');
    $searchClass = $request->query->get('search_class');
    $searchSession = $request->query->get('search_session');

    $queryBuilder = $internshipRepository->createQueryBuilder('i')
        ->where('i.IsVerified = true');

    $needJoinUser = $searchName || $searchClass;
    
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
            ->join('u.grade', 'g')
            ->andWhere('g.id = :searchClass')
            ->setParameter('searchClass', $searchClass);
    }

    if ($searchSession) {
        $queryBuilder
            ->andWhere('i.session = :searchSession')
            ->setParameter('searchSession', $searchSession);
    }

    $internships = $queryBuilder->getQuery()->getResult();

    return $this->generatePdf($internships, 'stages_verified');
}

    // Export de tous les stages vérifiés (sans pagination)
    #[Route('/export/verified/all', name: 'app_internship_export_verified_all', methods: ['GET'])]
    public function exportAllVerified(Request $request, InternshipRepository $internshipRepository): Response
    {
        $searchName = $request->query->get('search_name');
        $searchClass = $request->query->get('search_class');
        $searchSession = $request->query->get('search_session');

        $queryBuilder = $internshipRepository->createQueryBuilder('i')
            ->where('i.IsVerified = true');

        $needJoinUser = $searchName || $searchClass;
        
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
                ->join('u.grade', 'g')
                ->andWhere('g.id = :searchClass')
                ->setParameter('searchClass', $searchClass);
        }

        if ($searchSession) {
            $queryBuilder
                ->andWhere('i.session = :searchSession')
                ->setParameter('searchSession', $searchSession);
        }

        $internships = $queryBuilder->getQuery()->getResult();

        return $this->generatePdf($internships, 'stages_verified_all');
    }

    // Export des stages non vérifiés (sans pagination)
    #[Route('/export/unverified/all', name: 'app_internship_export_unverified_all', methods: ['GET'])]
    public function exportAllUnverified(Request $request, InternshipRepository $internshipRepository): Response
    {
        $searchName = $request->query->get('search_name');
        $searchClass = $request->query->get('search_class');
        $searchSession = $request->query->get('search_session');

        $queryBuilder = $internshipRepository->createQueryBuilder('i')
            ->where('i.IsVerified = false');

        $needJoinUser = $searchName || $searchClass;
        
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
                ->join('u.grade', 'g')
                ->andWhere('g.id = :searchClass')
                ->setParameter('searchClass', $searchClass);
        }

        if ($searchSession) {
            $queryBuilder
                ->andWhere('i.session = :searchSession')
                ->setParameter('searchSession', $searchSession);
        }

        $internships = $queryBuilder->getQuery()->getResult();

        return $this->generatePdf($internships, 'stages_unverified_all');
    }

    // Fonction privée pour générer le PDF
    private function generatePdf(array $internships, string $filenamePrefix): Response
    {
        $pdf = new \TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Liste des Stages', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 12);

    $html = '
    <table border="1" cellpadding="4">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th><b>Etudiant</b></th>
                <th><b>Classe</b></th>
                <th><b>Entreprise</b></th>
                <th><b>Session</b></th>
                <th><b>Dates</b></th>
            </tr>
        </thead>
        <tbody>';

    foreach ($internships as $internship) {
        // Combine les dates de début et de fin
        $dateDebut = $internship->getDateDebut()->format('d/m/Y');
        $dateFin = $internship->getDateFin()->format('d/m/Y');
        $dates = $dateDebut . ' à ' . $dateFin;

        // Récupération de la session
        $session = $internship->getSession();
        $sessionName = $session ? $session->getSessionList() : 'Non définie';

        $html .= sprintf('
            <tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
            </tr>',
            htmlspecialchars($internship->getRelation()->getFirstName() . ' ' . $internship->getRelation()->getLastName()),
            htmlspecialchars(implode(", ", array_map(fn($grade) => $grade->getClassName(), $internship->getRelation()->getGrade()->toArray()))),
            htmlspecialchars($internship->getCompany()->getName()),
            htmlspecialchars($sessionName),
            htmlspecialchars($dates)
        );
    }

    $html .= '</tbody></table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = $filenamePrefix . '_' . date('Ymd_His') . '.pdf';
    return new Response($pdf->Output($filename, 'S'), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
    }


#[Route('/export/unverified/page', name: 'app_internship_export_unverified_page', methods: ['GET'])]
public function exportUnverifiedPage(Request $request, InternshipRepository $internshipRepository): Response
{
    $searchName = $request->query->get('search_name');
    $searchClass = $request->query->get('search_class');
    $searchSession = $request->query->get('search_session');
    $page = max(1, (int) $request->query->get('page', 1));
    $limit = 15;
    $offset = ($page - 1) * $limit;

    $queryBuilder = $internshipRepository->createQueryBuilder('i')
        ->where('i.IsVerified = false');

    $needJoinUser = $searchName || $searchClass;
    
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
            ->join('u.grade', 'g')
            ->andWhere('g.id = :searchClass')
            ->setParameter('searchClass', $searchClass);
    }

    if ($searchSession) {
        $queryBuilder
            ->andWhere('i.session = :searchSession')
            ->setParameter('searchSession', $searchSession);
    }

    // Appliquer la pagination
    $queryBuilder->setFirstResult($offset)
                ->setMaxResults($limit);

    $internships = $queryBuilder->getQuery()->getResult();

    return $this->generatePdf($internships, 'stages_non_valides_page_' . $page);
}
}