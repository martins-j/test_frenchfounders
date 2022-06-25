<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Utilisateur;
use Nexy\Slack\Client;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SendController.
 */
class SendController extends AbstractController
{
    /**
     * Envoies un e-mail à l'utilisateur
     * 
     * @Route("/mail", name="mail", methods={"POST"})
     *
     * @param MailerInterface $mailer
     * @param Utilisateur     $utilisateur
     *
     * @return void
     */
    public function sendEmail(MailerInterface $mailer, Utilisateur $utilisateur): void
    {
        $email = (new TemplatedEmail())
            ->from($this->getParameter('mailer_default_sender'))
            ->to(new Address(
                $utilisateur->getEmail(),
                sprintf('%1$s %2$s', strtoupper($utilisateur->getNom()), $utilisateur->getPrenom())
            ))
            ->subject('Nouvelle inscription')
            ->htmlTemplate('utilisateur/email.html.twig')
            ->context([
                'utilisateur' => $utilisateur
            ])
        ;

        $mailer->send($email);
    }

    /**
     * Envoies un message / notification sur Slack
     * 
     * @Route("/slack", name="slack", methods={"POST"})
     * 
     * @param Client      $slack
     * @param Utilisateur $utilisateur
     * 
     * @return void
     */
    public function sendSlackNotification(Client $slack, Utilisateur $utilisateur): void
    {
        $message = $slack->createMessage()
            ->setText(sprintf('Un nouvel utilisateur a été créé : id = %d', $utilisateur->getId()))
        ;

        $slack->sendMessage($message);
    }
}
