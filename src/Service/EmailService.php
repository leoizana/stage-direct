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
        $result = "00";
        $email = (new Email())
            ->from('lgermain@ik.me')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo('no-reply@ik.me')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text($text)
            // ->html('<p>See Twig integration for better HTML integration!</p>')
            ;
            $this->mailer->send($email);
            return "00"; // Succès
        } catch (TransportExceptionInterface $e) {
            // Log de l'erreur pour diagnostic
            error_log('Erreur d\'envoi d\'e-mail : ' . $e->getMessage());
            return "01"; // Échec
        }

    }
}