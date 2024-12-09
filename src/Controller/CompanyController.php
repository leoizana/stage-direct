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
    public function index(CompanyRepository $companyRepository): Response
    {
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son compte
        $user = $this->getUser();
        
        if ($user && !$user->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
        }
        return $this->render('company/index.html.twig', [
            'companies' => $companyRepository->findAll(),
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
}
