<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\Trait\UtilsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseTestCase extends KernelTestCase
{
    use UtilsTrait;

    protected function setUp(): void
    {
        self::bootKernel();
    }
}
