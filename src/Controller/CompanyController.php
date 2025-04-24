<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/entreprise')]
final class CompanyController extends AbstractController
{
    #[Route(name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository, Request $request): Response
{
    $User = $this->getUser();
    if ($User && !$User->getIsVerified()) {
        $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
    }

    $limit = 15;
    $page = max(1, (int) $request->query->get('page', 1));
    $offset = ($page - 1) * $limit;

    $searchName = $request->query->get('name');
    $searchCityOrZip = $request->query->get('city_zip');

    $queryBuilder = $companyRepository->createQueryBuilder('c')
        ->where('c.IsVerified = true');

    if ($searchName) {
        $queryBuilder
        ->andWhere('LOWER(c.name) LIKE :name')
        ->setParameter('name', '%' . strtolower($searchName) . '%');
}


    if ($searchCityOrZip) {
        $queryBuilder
        ->andWhere('LOWER(c.city) LIKE :cityZip OR LOWER(c.zipCode) LIKE :cityZip')
        ->setParameter('cityZip', '%' . strtolower($searchCityOrZip) . '%');
}

    // Clone pour compter
    $countQueryBuilder = clone $queryBuilder;
    $result = $countQueryBuilder
        ->select('COUNT(c.id) as total')
        ->getQuery()
        ->getOneOrNullResult();

    $total = $result['total'] ?? 0;
    $maxPages = max(1, ceil($total / $limit));

    // Redirection si la page demandée est trop grande
    if ($page > $maxPages) {
        return $this->redirectToRoute('app_company_index', [
            'page' => $maxPages,
            'name' => $searchName,
            'city_zip' => $searchCityOrZip
        ]);
    }

    // Résultats paginés
    $companies = $queryBuilder
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

    return $this->render('company/index.html.twig', [
        'companies' => $companies,
        'total' => $total,
        'limit' => $limit,
        'page' => $page,
        'pages' => $maxPages,
        'searchName' => $searchName,
        'searchCityZip' => $searchCityOrZip,
    ]);
}

#[Route('/validation', name: 'app_company_validation', methods: ['GET'])]
public function validation(CompanyRepository $companyRepository, Request $request): Response
{
    $User = $this->getUser();
    if ($User && !$User->getIsVerified()) {
        $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
    }

    $limit = 15;
    $page = max(1, (int) $request->query->get('page', 1));
    $offset = ($page - 1) * $limit;

    $searchName = $request->query->get('name');
    $searchCityOrZip = $request->query->get('city_zip');

    $roles = $User?->getRoles() ?? [];

    $queryBuilder = $companyRepository->createQueryBuilder('c')
        ->where('c.IsVerified = false OR c.IsVerified IS NULL');

    if (in_array('ROLE_STUDENT', $roles)) {
        $queryBuilder
            ->andWhere('c.relation = :User')
            ->setParameter('User', $User);
    }

    if ($searchName) {
        $queryBuilder
            ->andWhere('LOWER(c.name) LIKE :name')
            ->setParameter('name', '%' . strtolower($searchName) . '%');
    }

    if ($searchCityOrZip) {
        $queryBuilder
            ->andWhere('LOWER(c.city) LIKE :cityZip OR LOWER(c.zipCode) LIKE :cityZip')
            ->setParameter('cityZip', '%' . strtolower($searchCityOrZip) . '%');
    }

    // Clonage pour le COUNT
    $countQueryBuilder = clone $queryBuilder;
    $countQueryBuilder->select('COUNT(c.id) as count');

    if (in_array('ROLE_STUDENT', $roles)) {
        $countQueryBuilder->setParameter('User', $User);
    }
    if ($searchName) {
        $countQueryBuilder->setParameter('name', '%' . strtolower($searchName) . '%');
    }
    if ($searchCityOrZip) {
        $countQueryBuilder->setParameter('cityZip', '%' . strtolower($searchCityOrZip) . '%');
    }

    $countResult = $countQueryBuilder
        ->getQuery()
        ->getOneOrNullResult();

    $total = (int) ($countResult['count'] ?? 0);

    $companies = $queryBuilder
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

    return $this->render('company/validation.html.twig', [
        'companies' => $companies,
        'total' => $total,
        'limit' => $limit,
        'page' => $page,
        'pages' => ceil($total / $limit),
        'searchName' => $searchName,
        'searchCityZip' => $searchCityOrZip,
        'currentRoute' => 'app_company_validation',
    ]);
}





#[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    // Vérifie si l'utilisateur est connecté
    $User = $this->getUser();
    if (!$User) {
        $this->addFlash('error', 'Vous devez être connecté pour créer une entreprise.');
        return $this->redirectToRoute('app_login');
    }

    // Créer une nouvelle entreprise et l'associer à l'utilisateur connecté
    $company = new Company();
    $company->setVerified(false);  // L'entreprise n'est pas encore validée
    $company->setRelation($User); // Associe l'utilisateur connecté à l'entreprise

    // Créer et traiter le formulaire de l'entreprise
    $form = $this->createForm(CompanyType::class, $company);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persister l'entreprise dans la base de données
        $entityManager->persist($company);
        $entityManager->flush();

        // Afficher un message de succès
        $this->addFlash('success', 'Entreprise soumise, attente de validation.');

        // Rediriger vers la liste des entreprises
        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }

    // Rendre la vue du formulaire pour l'ajout d'une entreprise
    return $this->render('company/new.html.twig', [
        'company' => $company,
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
    
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son compte
        $User = $this->getUser();
        
        if ($User && !$User->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
        }
        return $this->render('company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    // Vérifie si l'utilisateur est connecté et a le bon rôle
    if (!$this->isGranted('ROLE_TEACHER') && ($company->getRelation() !== $user)) {
        $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour modifier cette entreprise.');
        return $this->redirectToRoute('app_index'); // Redirige vers la page d'accueil si l'utilisateur n'a pas accès
    }

    // Vérifier si l'utilisateur est connecté et si son compte est vérifié
    if ($user && !$user->getIsVerified()) {
        $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
    }

    $form = $this->createForm(CompanyType::class, $company);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('company/edit.html.twig', [
        'company' => $company,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son compte
        $User = $this->getUser();
        
        if ($User && !$User->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
        }
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/company/export', name: 'app_company_export')]
    public function export(Request $request, CompanyRepository $companyRepository): Response
    {
        $searchName = $request->query->get('name');
        $searchCityOrZip = $request->query->get('city_zip');
    
        $limit = 15;
        $page = max(1, (int) $request->query->get('page', 1));
        $offset = ($page - 1) * $limit;
        $companies = $companyRepository->findFiltered($searchName, $searchCityOrZip, $limit, $offset, true);

    
        return $this->generatePdf($companies, 'entreprises_page_' . $page);
    }
    
    #[Route('/company/export/all', name: 'app_company_export_all')]
    public function exportAll(Request $request, CompanyRepository $companyRepository): Response
    {
        $searchName = $request->query->get('name');
        $searchCityOrZip = $request->query->get('city_zip');
    
        $companies = $companyRepository->findFiltered($searchName, $searchCityOrZip, null, null, true);

    
        return $this->generatePdf($companies, 'entreprises_all');
    }

    #[Route('/company/export/unverified', name: 'app_company_export_unverified')]
public function exportUnverified(Request $request, CompanyRepository $companyRepository): Response
{
    $searchName = $request->query->get('name');
    $searchCityOrZip = $request->query->get('city_zip');

    $companies = $companyRepository->findFiltered($searchName, $searchCityOrZip, null, null, false); // false = uniquement non vérifiées

    return $this->generatePdf($companies, 'entreprises_non_verifiees');
}
    
    // 🛠️ Fonction privée partagée pour éviter de dupliquer le code
    private function generatePdf(array $companies, string $filenamePrefix): Response
    {
        $pdf = new \TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Liste des Entreprises', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 12);
    
        $html = '
        <table border="1" cellpadding="4">
            <thead>
                <tr style="background-color:#f2f2f2;">
                    <th><b>Nom</b></th>
                    <th><b>Ville</b></th>
                    <th><b>Rue</b></th>
                    <th><b>Code Postal</b></th>
                    <th><b>Pays</b></th>
                    <th><b>Téléphone</b></th>
                    <th><b>Email</b></th>
                </tr>
            </thead>
            <tbody>';
    
        foreach ($companies as $company) {
            $html .= sprintf('
                <tr>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                </tr>',
                htmlspecialchars($company->getName()),
                htmlspecialchars($company->getCity()),
                htmlspecialchars($company->getStreet()),
                htmlspecialchars($company->getZipCode()),
                htmlspecialchars($company->getCountry()),
                htmlspecialchars($company->getPhone()),
                htmlspecialchars($company->getEmail()),
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
    #[Route('/entreprise/{id}/approve', name: 'app_company_approve', methods: ['POST'])]
    public function approve(Company $company, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER');
        $company->setVerified(true);
        $em->flush();
    
        $this->addFlash('success', message: 'Entreprise accepté et validé.');
        return $this->redirectToRoute('app_company_validation');
    }
    
    #[Route('/entreprise/{id}/reject', name: 'app_company_reject', methods: ['POST'])]
    public function reject(Company $company, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(attribute: 'ROLE_TEACHER');
        $em->remove($company);
        $em->flush();
    
        $this->addFlash('success', 'Entreprise refusé et supprimé.');
        return $this->redirectToRoute('app_company_validation');
    }

    
}
