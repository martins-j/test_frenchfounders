<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ApiTestCase.
 */
class ApiTestCase extends WebTestCase
{
    /** @var KernelBrowser $client */
    protected $client;

    /** @var EntityManagerInterface|EntityManager $entityManager */
    protected $entityManager;

    /**
     * Set up - création des objets sur lesquels on passera les tests
     * 
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
     * Tear down - nettoye les objets sur lesquels on avait passé les tests
     * 
     * @return void
     */
    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
