<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Domain\User\Entity\User;

class UserFactory
{
    public static function makeUser(
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

    public static function makeVerifiedUser(
        string $email,
        string $username,
        string $password
    ): User {
        $user = self::makeUser($email, $username, $password);
        $user->setEnabled(true);

        return $user;
    }
}
