<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'company_index')]
    public function index(CompanyRepository $companyRepository, Request $request): Response
    {
        $companies = $companyRepository->findAll();
        dump($request);



        return $this->render('company/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/add', name: 'company_add')]
    public function add(EntityManagerInterface $entityManager): Response
    {
        $sturno = new Company();
        $sturno->setName("STURNO");
        // $entityManager = $this-getDoctrine()->getManager();
        $entityManager->persist($sturno);
        $entityManager->flush();
        return new Response("Entreprise 'STURNIO' créee avec succès !")
        ;
    }

    #[Route('/update/{id}', name: 'company_update')]
    public function modificationCompany(int $id,EntityManagerInterface $entityManager, CompanyRepository $companyRepository): Response
    {
        $company = $companyRepository->find($id);
        $company->setName("TC Bois");

        //$entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($company);

        $entityManager ->flush();

        return new Response("Entreprise modifier avec succès !")
        ;
    }
    
    #[Route('/delete/{id}', name: 'company_delete')]
    public function deleteCompany(int $id,EntityManagerInterface $entityManager, CompanyRepository $companyRepository): Response
    {
        $company = $companyRepository->find($id);

        //$entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($company);
        $entityManager ->flush();

        return new Response("Entreprise supprimer avec succès !")
        ;
    }
}
