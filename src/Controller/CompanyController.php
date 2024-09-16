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
    #[Route('/add1', name: 'company_add1')]
    public function addFirst(EntityManagerInterface $entityManager): Response
    {
        $sturno = new Company();
        $sturno->setName("STURNO");
        // $entityManager = $this-getDoctrine()->getManager();
        $entityManager->persist($sturno);
        $entityManager->flush();
        return new Response("Entreprise 'STURNIO' créee avec succès !")
        ;
    }
}
