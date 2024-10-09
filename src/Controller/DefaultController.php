<?php

namespace App\Controller;

use App\Services\MessageGenerator;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/')]
class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(MessageGenerator $messageGenerator): Response
    {
        $message = $messageGenerator->getRandomMessage();
        
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'result' => 'OK',
            'message' => $message,
        ]);
    }
    #[Route('/sendmail/', name: 'app_sendmail')]
    public function sendmail(MailerInterface $mailer): Response
    {
        try{
            $result = '';
            $email = (new Email())
            ->from('lgermain@ik.me')
            ->to('leo.germain2005@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        $result = 'ok';
        } catch (\Throwable $th){
            $result = $th->GetMessage();
        }
        return $this->render('default/index.html.twig', [
            'result' => $result,
        ]);
    }
}
