<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\RoleRepository;
use App\Tests\Trait\UtilsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseTestCase extends KernelTestCase
{
    use UtilsTrait;

    protected const string DEFAULT_USER_EMAIL = 'test@test.io';

    protected readonly EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    public function makeUser(
        string $email = self::DEFAULT_USER_EMAIL,
        string $username = 'Test',
        string $password = 'test1234',
        bool $enabled = true,
    ): User {
        $roleRepository = static::getContainer()->get(RoleRepository::class);
        $roleUser = $roleRepository->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);

        $user = UserFactory::makeVerifiedUserWithRole(
            email: $email,
            username: $username,
            password: $password,
            role: $roleUser
        );

        if (false === $enabled) {
            $user->setEnabled(false);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
