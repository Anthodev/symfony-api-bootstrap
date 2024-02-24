<?php

declare(strict_types=1);

namespace App\Domain\User\EventListener;

use App\Domain\User\Entity\User;
use App\Domain\User\Security\PasswordChanger;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: Events::prePersist, entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: Events::preUpdate, entity: User::class)]
readonly class UserHashPassword
{
    public function __construct(
        private PasswordChanger $passwordChanger,
    ) {
    }

    public function prePersist(User $user): void
    {
        $this->hashPassword($user);
    }

    public function preUpdate(User $user): void
    {
        $this->hashPassword($user);

        $user->setUpdatedAt(new \DateTime());
    }

    private function hashPassword(User $user): void
    {
        if (null === $user->getPlainPassword()) {
            return;
        }

        $this->passwordChanger->changePassword($user);
    }
}
