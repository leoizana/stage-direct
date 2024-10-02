<?php

namespace App\Controller;
use App\Form\CompanyType;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/company')]
class CompanyController extends AbstractController
{   
    /* PHP version < 8
    private $companyRepository;
    public function __construct(CompanyRepository $companyRepository){
        $this->companyRepository = $companyRepository;
    }
        */
    // PHP version >= 8
    public function __construct(
        private CompanyRepository $companyRepository, 
        private FormFactoryInterface $formFactory, 
        private  EntityManagerInterface $entityManager){
    }

    #[Route('/', name: 'company_index')]
    public function index( Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_STUDENT');
        $companies = $this->companyRepository->findAll();
        dump($request);



        return $this->render('company/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/update/{id}', name: 'company_update')]
    public function modificationCompany(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER');
        $company = $this->companyRepository->find($id);
        $company->setName("TC Bois");

        //$entityManager = $this->getDoctrine()->getManager();
        $this->entityManager->persist($company);

        $this->entityManager ->flush();

        return new Response("Entreprise modifier avec succès !")
        ;
    }
    
    #[Route('/delete/{id}', name: 'company_delete')]
    public function deleteCompany(int $id,): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER');
        $company = $this->companyRepository->find($id);

        //$entityManager = $this->getDoctrine()->getManager();
        $this->entityManager->remove($company);
        $this->entityManager ->flush();

        return new Response("Entreprise supprimer avec succès !")
        ;
    }
    #[Route('/add', name: 'company_add')]
    public function add(Request $request,): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TEACHER');
        $company = new Company();
        $form = $this->formFactory->create(CompanyType::class, $company);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($company);
            $this->entityManager->flush();

            return $this->redirectToRoute('company_index');
        }

        return $this->render('company/create.html.twig', ['form' => $form->createView()]);
    }
}
