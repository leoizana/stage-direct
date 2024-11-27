<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompanyController extends AbstractController
{
    #[Route('/entreprise', name: 'app_company')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son compte


        $user = $this->getUser();
        
        if ($user && !$user->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
        }

        return $this->render('company/index.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
      
    }
}
