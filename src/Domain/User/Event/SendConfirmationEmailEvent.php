<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\User\Entity\PendingRegistration;
use App\Domain\User\Enum\UserEventEnum;
use Symfony\Contracts\EventDispatcher\Event;

class SendConfirmationEmailEvent extends Event
{
    public const string NAME = UserEventEnum::SEND_CONFIRMATION_EMAIL->value;

    public function __construct(
        private readonly PendingRegistration $pendingRegistration,
    ) {
    }

    public function getPendingRegistration(): PendingRegistration
    {
        return $this->pendingRegistration;
    }
}
