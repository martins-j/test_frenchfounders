<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UtilisateurController.
 * 
 * @Route("/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /** @var EntityManagerInterface $entityManager */
    private EntityManagerInterface $entityManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private UserPasswordEncoderInterface $passwordEncoder;

    /** @var ValidationService $validator */
    private ValidationService $validator;

    /** @var SendController $mailer */
    private SendController $mailer;

    /** @var HomeController $homeController */
    private HomeController $homeController;

    /**
     * UtilisateurController construct.
     * 
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ValidationService            $validator
     * @param SendController               $mailer
     * @param HomeController               $homeController
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidationService $validator,
        SendController $mailer,
        HomeController $homeController
    ) {
        $this->entityManager   = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator       = $validator;
        $this->mailer          = $mailer;
        $this->homeController  = $homeController;
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $parameters = $this->preapreParameters($request);

        if(!$this->errors($parameters)) {
            $utilisateur = new Utilisateur();

            $utilisateur
                ->setNom($parameters['nom'])
                ->setPrenom($parameters['prenom'])
                ->setSociete($parameters['societe'])
                ->setEmail($parameters['email'])
                ->setPassword($$this->passwordEncoder->encodePassword($utilisateur, $parameters['password']))
            ;

            if($this->validator->validate($utilisateur)) {
                //$this->entityManager->persist($utilisateur);
                //$this->entityManager->flush();

                $this->mailer->sendEmail([
                    'nom'    => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                    'email'  => $utilisateur->getEmail()
                ]);

                return new JsonResponse([
                    'result' => $utilisateur
                ]);
            }
    
            return new JsonResponse([
                    'erreurs' => $this->validator->getErrors()
                ],
               400
            );
        }
    }

    /**
     * @param Request $request
     * 
     * @return array
     */
    private function preapreParameters(Request $request): array
    {
        return [
            'nom'                  => $request->request->get('nom'),
            'prenom'               => $request->request->get('prenom'),
            'societe'              => $request->request->get('societe'),
            'email'                => $request->request->get('email'),
            'password'             => $request->request->get('password'),
            'passwordConfirmation' => $request->request->get('password_confirmation')
        ];
    }

    /**
     * @param array $parameters
     * 
     * @return array
     */
    private function errors(array $parameters): array
    {
        $errors = [];
        
        if ($parameters['password'] !== $parameters['passwordConfirmation']) {
            $errors[] = "Les mots de passe ne sont pas identiques.";
        }

        if (6 > strlen($parameters['password'])) {
            $errors[] = "Le mot de passe doit comporter au moins 6 caractères.";
        }

        return $errors;
    }

    /**
     * @Route("/connecte", name="connecte", methods={"GET"})
     * 
     * @return JsonResponse
     */
    public function connecte(): JsonResponse
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
