<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EmailService {

    private $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }


    public function sendEmail($subject, $text, $to): string
    {
        try {
            // Par défaut, "00" indique un succès
            $result = "00";
    
            // Créez l'email avec un nom d'affichage pour l'expéditeur
            $email = (new Email())
                ->from('lgermain@ik.me', 'Stage-Direct')  // Nom personnalisé dans l'expéditeur
                ->to($to)
                ->replyTo('no-reply@ik.me')
                ->subject($subject)
                ->text($text);
    
            // Option pour ajouter une image embarquée dans l'email (si vous avez une image locale)
            $imagePath = '/public/images/stage2.png';  // Remplacez par le chemin réel de votre image
            $email->embedFromPath($imagePath, 'profile_picture');  // Le 2e argument est l'identifiant de l'image
    
            // Vous pouvez également envoyer un HTML, si nécessaire
            // $email->html('<p>Votre contenu HTML ici <img src="cid:profile_picture" alt="Profile Picture" /></p>');
    
            // Envoi de l'email
            $this->mailer->send($email);
            return "00"; // Succès
        } catch (TransportExceptionInterface $e) {
            // Log de l'erreur pour diagnostic
            error_log('Erreur d\'envoi d\'e-mail : ' . $e->getMessage());
            return "01"; // Échec
        }

    }
}