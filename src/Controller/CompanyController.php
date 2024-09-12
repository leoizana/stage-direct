<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'company_index')]
    public function index(): Response
    {
        return $this->render('company/index.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
    }
    #[Route('/add1', name: 'company_add1')]
    public function addFirst(): Response
    {
        return new Response("Entreprise 'STURNIO' créee avec succès !")
        ;
    }
}
