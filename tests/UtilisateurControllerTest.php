<?php

namespace App\Tests;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UtilisateurControllerTest
 */
class UtilisateurControllerTest extends WebTestCase
{
    /** @var KernelBrowser $client */
    private $client;

    /** @var EntityManagerInterface|EntityManager $entityManager */
    private $entityManager;

    /** @var UtilisateurRepository|MockObject $utilisateurRepository */
    private $utilisateurRepository;

    /**
     * Tests create success
     * 
     * @return void
     */
    public function testCreateSuccess(): void
    {
        $this->client->request(
            'POST',
            '/create',
            [],
            [],
            [],
            json_encode([
                'nom'                   => 'nom',
                'prenom'                => 'prenom',
                'societe'               => 'societe',
                'email'                 => 'foo@bar',
                'password'              => 'password',
                'password_confirmation' => 'password0'
            ])
        );
        
        dump(json_decode($this->client->getResponse()->getContent(), true));

        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @return array
     */
    public function utilisateurs(): array
    {
        return [
            [ '0@foo.bar'   , 'password0'   , 1, 200 ],
            [ '0@foo.bar'   , 'bad password', 1, 400 ],
            [ 'not existing', 'password'    , 0, 400 ]
        ];
    }

    /**
     * Tests login
     * 
     * @dataProvider utilisateurs
     * 
     * @param string $email
     * @param string $password
     * @param int    $count
     * @param int    $httpCode
     * 
     * @return void
     */
    public function testLogin(string $email, string $password, int $count, int $httpCode): void
    {
        /** @var Utilisateurs[] $utilisateurs */
        $utilisateurs = $this->entityManager->getRepository(Utilisateur::class)->findBy(['email' => $email]);

        $this->assertCount($count, $utilisateurs);

        $this->client->request(
            'POST',
            '/login',
            [
                'email'    => $email,
                'password' => $password
            ]

        );

        $this->assertResponseStatusCodeSame($httpCode);
    }

    /**
     * Tests logout
     * 
     * @return void
     */
    public function testLogout(): void
    {
        $this->client->request(
            'POST',
            '/logout'
        );

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * Tests get utilisateur connecte
     * 
     * @return void
     */
    public function testGetUtilisateurConnecte(): void
    {
        $nom      = 'Nom0';
        $prenom   = 'Prenom0';
        $societe  = 'Societe0';
        $email    = '0@foo.bar';
        $password = 'password0';

        $this->client->request(
            'POST',
            '/login',
            [
                'email'    => $email,
                'password' => $password
            ]

        );

        $this->assertResponseStatusCodeSame(200);

        $this->client->request(
            'GET',
            '/connecte'
        );

        $this->assertResponseStatusCodeSame(200);

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('Utilisateur connecté', $content);
        $this->assertSame($nom, $content['Utilisateur connecté']['nom']);
        $this->assertSame($prenom, $content['Utilisateur connecté']['prenom']);
        $this->assertSame($societe, $content['Utilisateur connecté']['societe']);
        $this->assertSame($email, $content['Utilisateur connecté']['email']);
    }

    /** 
     * Tests aucun utilisateur connecte
     * 
     * @return void
     */
    public function testAucunUtilisateurConnecte(): void
    {
        $email    = '0@foo.bar';
        $password = 'password0';

        $this->client->request(
            'POST',
            '/login',
            [
                'email'    => $email,
                'password' => $password
            ]

        );

        $this->assertResponseStatusCodeSame(200);

        $this->client->request(
            'POST',
            '/logout'
        );

        $this->assertResponseStatusCodeSame(302);

        $this->client->request(
            'GET',
            '/connecte'
        );

        $this->assertResponseStatusCodeSame(200);

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('Utilisateur connecté', $content);
        $this->assertSame('Aucun utilisateur connecté en ce moment.', $content['Utilisateur connecté']);
    }

    /**
     * Roles
     * 
     * return array
     */
    public function roles(): array
    {
        return [
            [ ['ROLE_ADMIN'], 200, 0, 'Access authorized!' ],
            [ ['ROLE_USER'] , 403, 1, 'Access Denied!'     ]
        ];
    }

    /** 
     * Tests access to admin
     * 
     * @dataProvider roles
     * 
     * @param array  $roles
     * @param int    $httpCode
     * @param int    $count
     * @param string $error
     * 
     * @return void
     */
    public function testAdminAccess(array $roles, int $httpCode, int $count, string $error): void
    {
        $email    = '0@foo.bar';
        $password = 'password0';

        /** @var Utilisateur|null $utilisateur */
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        $utilisateur->setRoles($roles);
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/login',
            [
                'email'    => $email,
                'password' => $password
            ]

        );

        $this->client->request(
            'POST',
            '/admin',
        );

        $this->assertResponseStatusCodeSame($httpCode);

        $content = json_decode($this->client->getResponse()->getContent(), true);

        //$this->assertArrayHasKey('error', $content);
        //$this->assertCount($count, $content['error']);
        //$this->assertSame($error, $content['error']);
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
