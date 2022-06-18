<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SendController
 */
class SendController extends AbstractController
{
    /** @var MailerInterface $mailer */
    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(
        MailerInterface $mailer
    ) {
        $this->mailer = $mailer;
    }

    /**
    * @Route("/mail", name="mail", methods={"POST"})
    *
    * @param mixed $data
    *
    * @return Response
    */
    public function sendEmail($data): void
    {
        $email = (new Email())
            ->from('foo@bar.fr')
            ->to(new Address($data['email'], sprintf('%1$s %2$s', $data['nom'], $data['prenom'])))
            ->subject('Enregistrement')
            ->text('Hey! Learn the best practices of building HTML emails and play with ready-to-go templates. Mailtrap’s Guide on How to Build HTML Email is live on our blog')
            ->html(
                '<html>
                    <body>
                        <p><br>Hey</br>
                            Learn the best practices of building HTML emails and play with ready-to-go templates.</p>
                        <p><a href="/blog/build-html-email/">Mailtrap’s Guide on How to Build HTML Email</a> is live on our blog</p>
                    </body>
                </html>'
            )
        ;

        $this->mailer->send($email);
    }
}
