<?php

declare(strict_types=1);

namespace App\Domain\User\UseCase;

use App\Domain\User\Dto\RegisterPendingRegistrationInput;
use App\Domain\User\Entity\Role;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Enum\UserEventEnum;
use App\Domain\User\Event\SendConfirmationEmailEvent;
use App\Domain\User\Factory\PendingRegistrationFactory;
use App\Domain\User\Repository\PendingRegistrationRepository;
use App\Domain\User\Repository\RoleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class RegisterPendingRegistrationUseCase
{
    public function __construct(
        private PendingRegistrationRepository $pendingRegistrationRepository,
        private RoleRepository $roleRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function registerPendingRegistration(RegisterPendingRegistrationInput $registerUserInput): void
    {
        $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
            $registerUserInput->getEmail(),
            $registerUserInput->getUsername(),
            $registerUserInput->getPassword()
        );

        /** @var Role $role */
        $role = $this->roleRepository->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);

        $pendingRegistration->setRole($role);

        $token = md5($pendingRegistration->getEmail());
        $pendingRegistration->setToken($token);

        $this->pendingRegistrationRepository->save($pendingRegistration);
        $this->eventDispatcher->dispatch(new SendConfirmationEmailEvent($pendingRegistration), UserEventEnum::SEND_CONFIRMATION_EMAIL->value);
    }
}
