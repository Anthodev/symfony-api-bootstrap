<?php

declare(strict_types=1);

namespace App\Domain\User\EventListener;

use App\Domain\User\Entity\PendingRegistration;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: Events::prePersist, entity: PendingRegistration::class)]
#[AsEntityListener(event: Events::preUpdate, method: Events::preUpdate, entity: PendingRegistration::class)]
readonly class PendingRegistrationHashPassword
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function prePersist(PendingRegistration $pendingRegistration): void
    {
        $this->hashPassword($pendingRegistration);

        if (method_exists($pendingRegistration, 'setExpiresAt')) {
            $pendingRegistration->setExpiresAt(new \DateTime());
        }
    }

    public function preUpdate(PendingRegistration $pendingRegistration): void
    {
        $this->hashPassword($pendingRegistration);
    }

    private function hashPassword(PendingRegistration $pendingRegistration): void
    {
        if (null === $pendingRegistration->getPlainPassword()) {
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($pendingRegistration, $pendingRegistration->getPlainPassword());
        $pendingRegistration->setPassword($hashedPassword);
        $pendingRegistration->eraseCredentials();
    }
}
