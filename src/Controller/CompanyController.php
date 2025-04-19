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
    $user = $this->getUser();
    if ($user && !$user->getIsVerified()) {
        $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
    }

    $limit = 15;
    $page = max(1, (int) $request->query->get('page', 1));
    $offset = ($page - 1) * $limit;

    $searchName = $request->query->get('name');
    $searchCityOrZip = $request->query->get('city_zip');

    $companies = $companyRepository->findFiltered($searchName, $searchCityOrZip, $limit, $offset);
    $total = $companyRepository->countFiltered($searchName, $searchCityOrZip);

    return $this->render('company/index.html.twig', [
        'companies' => $companies,
        'total' => $total,
        'limit' => $limit,
        'page' => $page,
        'pages' => ceil($total / $limit),
        'searchName' => $searchName,
        'searchCityZip' => $searchCityOrZip,
    ]);
}

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
    
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son compte
        $user = $this->getUser();
        
        if ($user && !$user->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
        }
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
    
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son compte
        $user = $this->getUser();
        
        if ($user && !$user->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
        }
        return $this->render('company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas l\'accès requis pour consulter cette page.');
            return $this->redirectToRoute('app_index'); // Remplacez 'app_index' par la route de votre page d'accueil ou index
        }
    
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son compte
        $user = $this->getUser();
        
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
        $user = $this->getUser();
        
        if ($user && !$user->getIsVerified()) {
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
    
        $companies = $companyRepository->findFiltered($searchName, $searchCityOrZip, $limit, $offset);
    
        return $this->generatePdf($companies, 'entreprises_page_' . $page);
    }
    
    #[Route('/company/export/all', name: 'app_company_export_all')]
    public function exportAll(Request $request, CompanyRepository $companyRepository): Response
    {
        $searchName = $request->query->get('name');
        $searchCityOrZip = $request->query->get('city_zip');
    
        $companies = $companyRepository->findFiltered($searchName, $searchCityOrZip, null, null);
    
        return $this->generatePdf($companies, 'entreprises_all');
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
    

    
}
