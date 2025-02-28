<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son compte


        $user = $this->getUser();
        
        if ($user && !$user->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas vérifié. Veuillez vérifier votre email pour éviter la suppression.');
        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);


        ///////////////////////////////////////////////////////////////////////////////////////////////
    }

}
