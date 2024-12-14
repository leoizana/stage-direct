<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\File\File;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($subject, $text, $to): string
    {
        try {
            // Par défaut, "00" indique un succès
            $result = "00";

            // Créez l'email avec un nom d'affichage pour l'expéditeur
            $email = (new Email())
            ->from('lgermain@ik.me')  
            ->to($to)
            ->replyTo('no-reply@ik.me')
            ->subject($subject)
            ->text($text);

            // Envoi de l'email
            $this->mailer->send($email);
            return "00"; // Succès
        } catch (TransportExceptionInterface $e) {
            // Log de l'erreur pour diagnostic
            error_log('Erreur d\'envoi d\'e-mail : ' . $e->getMessage());
            return "01"; // Échec
        }
    }
    public function sendEmailWithAttachment($subject, $text, $to, File $pdfFile): string
    {
        try {
            // Par défaut, "00" indique un succès
            $result = "00";

            // Créez l'email avec un nom d'affichage pour l'expéditeur
            $email = (new Email())
            ->from('lgermain@ik.me')  
            ->to($to)
            ->replyTo('no-reply@ik.me')
            ->subject($subject)
                ->text($text)
                ->attachFromPath($pdfFile->getRealPath(), 'rapport_stage.pdf', 'application/pdf');  // Attacher le PDF

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

