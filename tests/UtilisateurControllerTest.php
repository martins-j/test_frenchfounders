<?php

namespace App\Tests;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilisateurControllerTest extends WebTestCase
{
    /** @var KernelBrowser $client */
    protected KernelBrowser $client;

    /** @var EntityManager $entityManager */
    protected EntityManager $entityManager;

    /**
     * Tests create success
     * 
     * @return void
     */
    public function testCreateSuccess(): void
    {
        $data = [
            'nom'                   => 'Doe',
            'prenom'                => 'John',
            'societe'               => 'Societe',
            'email'                 => 'john.doe@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password'
        ];

        $this->client->request(
            'POST',
            '/create',
            [],
            [],
            [],
            json_encode([
                $data
            ], true),
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $content = json_decode($this->client->getResponse()->getContent(), true);

        dump($content);die;
    }

    /**
     * @return void
     */
    public function testLoginSuccess(): void
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->findAll()[0];

        $data = [
            'email'    => $utilisateur->getEmail(),
            'password' => 'password'
        ];
        
        $this->client->request(
            'POST',
            '/login',
            [],
            [],
            [],
            json_encode([
                $data
            ], true)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
