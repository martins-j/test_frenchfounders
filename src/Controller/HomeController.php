<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController.
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     * 
     * @return JsonResponse
     */
    public function home(): JsonResponse
    {
        return new JsonResponse([
            'Bienvenue sur la page d\'accueil!'
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * 
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        return new JsonResponse([
            'Connexion réussie!'
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     */
    public function logout()
    {
    }

    /**
    * @Route("/admin", name="admin", methods={"POST"})
    *
    * @IsGranted("ROLE_ADMIN")
    *
    * @return JsonResponse
    */
    public function admin(): JsonResponse
    {
        return new JsonResponse([
            'Access authorized.'
        ]);
    }
}
