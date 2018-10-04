<?php

namespace App\Tests\Integration;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class IntegrationWebTestCase
 * @package App\Tests\Integration
 */
class IntegrationWebTestCase extends WebTestCase
{
    const BASE_URL = 'http://localhost/';

    /** @var Client */
    protected $client;

    public function setUp()
    {
        $headers = [
            'HTTP_ACCEPT' => 'application/json',
        ];
        $this->client = static::createClient([], $headers);
        $this->resetFixtures($this->client->getKernel());
    }

    /**
     * @param KernelInterface $kernel
     */
    protected function resetFixtures(KernelInterface $kernel)
    {
        $container = $kernel->getContainer();
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine')->getManager();
        FixturesLoader::resetDatabase($entityManager, $container);
    }
}