<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User\EventSubscriber;

use App\Domain\User\Enum\UserEventEnum;
use App\Domain\User\EventSubscriber\SendConfirmationEmailSubscriber;
use App\Domain\User\Service\UserMailer;

beforeEach(function () {
    $this->userMailer = $this->createMock(UserMailer::class);

    $this->sendConfirmationEmailSubscriber = new SendConfirmationEmailSubscriber(
        $this->userMailer,
    );
});

it('should return an array of subscribed events', function () {
    // When
    $subscribedEvents = SendConfirmationEmailSubscriber::getSubscribedEvents();

    // Then
    expect($subscribedEvents)
        ->toBeArray()
        ->toHaveKey(UserEventEnum::SEND_CONFIRMATION_EMAIL->value)
        ->and($subscribedEvents[UserEventEnum::SEND_CONFIRMATION_EMAIL->value])
            ->toBe('sendConfirmationEmail')
    ;
});
