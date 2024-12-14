<?php
namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class ReportController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/rapport', name: 'app_report')]
    public function index(Request $request, EmailService $emailService): Response
    {
        // Créer une instance du rapport et du formulaire
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde du rapport
            $report->setCreatedAt(new \DateTime());
            $this->entityManager->persist($report);
            $this->entityManager->flush();

            // Générer le PDF
            $pdfPath = $this->generatePdf($report);

            // Vérification de l'existence du fichier PDF
            $filesystem = new Filesystem();
            if (!$filesystem->exists($pdfPath)) {
                return new Response('Le fichier PDF n\'a pas pu être généré.', 500);
            }

            // Envoi de l'email avec le fichier attaché
            $subject = 'Rapport de Stage de ' . $report->getFirstName() . ' ' . $report->getLastName();
            $text = 'Voici le rapport de stage détaillé de ' . $report->getFirstName() . ' ' . $report->getLastName();
            $pdfFile = new File($pdfPath);

            // Envoi de l'email
            $result = $emailService->sendEmailWithAttachment($subject, $text, $report->getProfessorEmail(), $pdfFile);

            if ($result === '00') {
                return $this->redirectToRoute('app_report');
            } else {
                return new Response('Une erreur est survenue lors de l\'envoi de l\'email.', 500);
            }
        }

        return $this->render('report/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour générer le PDF (à remplacer par votre propre logique)
    private function generatePdf(Report $report): string
    {
        // Inclure la bibliothèque TCPDF
        $pdf = new \TCPDF();
        
        // Ajouter une page
        $pdf->AddPage();
        
        // Définir la police
        $pdf->SetFont('helvetica', 'B', 16); // Police pour le titre
        $pdf->Cell(0, 10, 'Rapport de Stage', 0, 1, 'C');  // Titre principal, centré
        $pdf->Ln(5);  // Saut de ligne
        
        // Informations sur l'étudiant (Nom et Prénom)
        $pdf->SetFont('helvetica', '', 12);  // Police normale
        $pdf->Cell(0, 10, 'Nom de l\'étudiant : ' . $report->getFirstName() . ' ' . $report->getLastName(), 0, 1);
        $pdf->Cell(0, 10, 'Session : ' . $report->getSession(), 0, 1);  // Session
        $pdf->Cell(0, 10, 'Classe : ' . $report->getClasse(), 0, 1);  // Classe
        $pdf->Ln(5);  // Saut de ligne
    
        // Titre du rapport
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Titre du rapport : ' . $report->getTitle(), 0, 1);
        $pdf->Ln(5);  // Saut de ligne
        
        // Contenu du rapport
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(0, 10, $report->getContent());  // Contenu du rapport sur plusieurs lignes
        $pdf->Ln(5);  // Saut de ligne
    
        // Remplacer la date de création par "Fait le : DATE"
        $pdf->Cell(0, 10, 'Fait le : ' . date('d/m/Y'), 0, 1);
        $pdf->Ln(10);  // Saut de ligne pour séparer le pied de page
    
        // Pied de page
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 10, 'Ce rapport a été généré automatiquement, ne pas modifier.', 0, 1, 'C');
        
        // Spécifier le chemin où le PDF sera sauvegardé
        $pdfPath = $this->getParameter('kernel.project_dir') . '/var/pdf/rapport_stage.pdf'; // Sauvegarde dans var/pdf
        
        // Vérifier si le dossier existe, sinon le créer
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->getParameter('kernel.project_dir') . '/var/pdf')) {
            $filesystem->mkdir($this->getParameter('kernel.project_dir') . '/var/pdf');
        }
        
        // Sauvegarder le PDF sur le disque
        $pdf->Output($pdfPath, 'F');  // 'F' pour save to file
        
        return $pdfPath;  // Retourne le chemin du PDF généré
    }

    

}
