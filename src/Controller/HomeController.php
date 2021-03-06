<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController.
 */
class HomeController extends AbstractController
{
    /**
     * Page d'accueil
     * 
     * @Route("/", name="home")
     * 
     * @return Response
     */
    public function home(): Response
    {
        return new Response(
            '<html><body>Bienvenue sur la page d\'accueil !</body></html>'
        );
    }

    /**
     * Login
     * 
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login()
    {
    }

    /**
     * Logout
     * 
     * @Route("/logout", name="logout", methods={"POST"})
     */
    public function logout()
    {
    }

    /**
     * Accès au contenu réservé à l'administrateur
     *      
     * @Route("/admin", name="admin", methods={"POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @return JsonResponse
     */
    public function admin(): JsonResponse
    {
        return $this->json([
                'result' => 'Access authorized.'
            ]
        );
    }
}
