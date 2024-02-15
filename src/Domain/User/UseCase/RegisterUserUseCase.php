<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\User\Dto\RegisterUserInput;
use App\Domain\User\Entity\Role;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\RoleRepository;
use App\Domain\User\Repository\UserRepository;

readonly class RegisterUserUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
    ) {
    }

    public function registerUser(RegisterUserInput $registerUserInput): void
    {
        $user = UserFactory::createUser(
            $registerUserInput->getEmail(),
            $registerUserInput->getUsername(),
            $registerUserInput->getPassword()
        );

        /** @var Role $role */
        $role = $this->roleRepository->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);

        $user->setRole($role);

        $this->userRepository->save($user);
    }
}
