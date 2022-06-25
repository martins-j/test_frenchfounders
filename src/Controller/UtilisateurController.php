<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Nexy\Slack\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UtilisateurController.
 */
class UtilisateurController extends AbstractController
{
    /** @var EntityManagerInterface $entityManager */
    private EntityManagerInterface $entityManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private UserPasswordEncoderInterface $passwordEncoder;

    /** @var ValidationService $validator */
    private ValidationService $validator;

    /** @var SendController $send */
    private SendController $send;

    /** @var HomeController $homeController */
    private HomeController $homeController;

    /**
     * UtilisateurController construct.
     * 
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ValidationService            $validator
     * @param SendController               $send
     * @param HomeController               $homeController
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidationService $validator,
        SendController $send,
        HomeController $homeController
    ) {
        $this->entityManager   = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator       = $validator;
        $this->send            = $send;
        $this->homeController  = $homeController;
    }

    /**
     * Créer un nouvel utilisateur
     * Une fois créé :
     *  - un message e-mail est envoyé au nouvel utilisateur
     *  - une notification est envoyée au groupe sur Slack
     * 
     * @Route("/create", name="create", methods={"POST"})
     * 
     * @param Request         $request
     * @param MailerInterface $mailer
     * @param Client          $slack
     * 
     * @return JsonResponse
     */
    public function create(Request $request, MailerInterface $mailer, Client $slack): JsonResponse
    {
        $parameters = $this->prepareParameters($request);

        if($this->errors($parameters)) {
            return new JsonResponse([
                    'Erreurs' => $this->errors($parameters)
                ],
                400
            );
        }

        $utilisateur = new Utilisateur();
        $utilisateur
            ->setNom($parameters['nom'])
            ->setPrenom($parameters['prenom'])
            ->setSociete($parameters['societe'])
            ->setEmail($parameters['email'])
            ->setPassword($parameters['password'])
        ;

        $passwordEncoded = $this->passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword());
        $utilisateur->setPassword($passwordEncoded);

        if($this->validator->validate($utilisateur)) {
            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            # Sending email
            $this->send->sendEmail($mailer, $utilisateur);

            # Sending slack notification
            $this->send->sendSlackNotification($slack, $utilisateur);

            return $this->json([
                'Utilisateur créé' => $utilisateur
            ],
            200,
            [],
            [
                'groups' => [
                    'utilisateur'
                ]
            ])
            ;
        }

        return new JsonResponse([
                'Erreurs' => $this->validator->getErrors()
            ],
            400
        );
    }

    /**
     * Prépare les données saisis pour la création de l'utilisateur
     * 
     * @param Request $request
     * 
     * @return array
     */
    private function prepareParameters(Request $request): array
    {
        return [
            'nom'                   => $request->request->get('nom'),
            'prenom'                => $request->request->get('prenom'),
            'societe'               => $request->request->get('societe'),
            'email'                 => $request->request->get('email'),
            'password'              => $request->request->get('password'),
            'password_confirmation' => $request->request->get('password_confirmation')
        ];
    }

    /**
     * Liste des erreurs lors de la céation de l'utilisateur
     * 
     * @param array $parameters
     * 
     * @return array
     */
    private function errors(array $parameters): array
    {
        $errors = [];

        if (!$parameters['password']) {
            $errors[] = "Le mot de passe ne peut pas être vide.";
        }

        if (6 > strlen($parameters['password'])) {
            $errors[] = "Le mot de passe doit comporter au moins 6 caractères.";
        }
        
        if ($parameters['password'] !== $parameters['password_confirmation']) {
            $errors[] = "Les mots de passe ne sont pas identiques.";
        }

        return $errors;
    }

    /**
     * Donne les informations de l'utilisateur connecté
     * 
     * @Route("/connected", name="connecte", methods={"GET"})
     * 
     * @return JsonResponse
     */
    public function connected(): JsonResponse
    {
        $this->homeController->login();
        
        return $this->json([
                'Utilisateur connecté' => $this->getUser() ? $this->getUser() : 'Aucun utilisateur connecté en ce moment.'
            ],
            200,
            [],
            [
                'groups' => [
                    'utilisateur'
                ]
            ],
        );
    }
}
