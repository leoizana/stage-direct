<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SchoolController extends AbstractController
{
    #[Route('/ecole', name: 'app_school')]
    public function index(): Response
    {
        return $this->render('school/index.html.twig', [
            'controller_name' => 'SchoolController',
        ]);
    }
}
