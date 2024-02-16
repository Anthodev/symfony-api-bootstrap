<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseWebTestCase extends WebTestCase
{
    protected readonly EntityManagerInterface $entityManager;
    protected static KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        static::$client = static::createClient();

        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }
}
