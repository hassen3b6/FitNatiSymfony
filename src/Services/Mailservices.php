<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class MailService
{
    private $mailer;
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function sendEmail($recipient, $subject, $content)
    {
        
        $transport = new Swift_SmtpTransport('smtp.mailtrap.io', 2525);
        $transport->setUsername('d97709fa0a9052'); 
        $transport->setPassword('36ec53b040a63e'); 

        $swiftMailer = new Swift_Mailer($transport);

        
        $message = (new Swift_Message($subject))
            ->setFrom('adminFitnatic@esprit.tn')
            ->setTo($recipient)
            ->setBody($content, 'text/html');

        // Send the message
        $result = $swiftMailer->send($message);

        // Log the result
        $this->logger->info('Email sent result: ' . $result);
    }

    public function sendEmailWithSymfonyMailer($recipient, $subject, $content)
    {
        // Utiliser Symfony Mailer pour envoyer un email
        $email = (new Email())
            ->from('adminFitnatic@esprit.tn')
            ->to($recipient)
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);

        // Log the result
        $this->logger->info('Symfony Mailer: Email sent successfully');
    }
}