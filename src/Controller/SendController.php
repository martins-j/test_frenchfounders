<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SendController
 */
class SendController extends AbstractController
{
    /**
    * @Route("/mail", name="mail", methods={"POST"})
    *
    * @param MailerInterface $mailer
    * @param array           $data
    *
    * @return Response
    */
    public function sendEmail(MailerInterface $mailer, array $data): void
    {
        $email = (new TemplatedEmail())
            ->from($this->getParameter('mailer_default_sender'))
            ->to(new Address($data['mail'], sprintf('%1$s %2$s', strtoupper($data['nom']), $data['prenom'])))
            ->subject('Nouvelle inscription')
            ->htmlTemplate('utilisateur/email.html.twig')
            ->context($data)
        ;

        $mailer->send($email);
    }
}
