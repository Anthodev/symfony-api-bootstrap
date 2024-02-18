<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\User\Entity\PendingRegistration;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\PendingRegistrationRepository;
use App\Domain\User\Repository\UserRepository;

readonly class ConfirmEmailUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private PendingRegistrationRepository $pendingRegistrationRepository,
    ) {
    }

    public function registerUser(
        PendingRegistration $pendingRegistration,
    ): void {
        $user = UserFactory::makeVerifiedUserFromPendingRegistration(
            $pendingRegistration,
        );

        $this->userRepository->save($user);
        $this->pendingRegistrationRepository->delete($pendingRegistration);
    }
}
