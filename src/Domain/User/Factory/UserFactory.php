<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Domain\User\Entity\User;

class UserFactory
{
    public static function createUser(
        string $email,
        string $username,
        string $password
    ): User {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPlainPassword($password);

        return $user;
    }

    public static function createVerifiedUser(
        string $email,
        string $username,
        string $password
    ): User {
        $user = self::createUser($email, $username, $password);
        $user->setEnabled(true);

        return $user;
    }
}
