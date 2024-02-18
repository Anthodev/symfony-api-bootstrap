<?php

declare(strict_types=1);

namespace App\Domain\User\EventSubscriber;

use App\Domain\User\Enum\UserEventEnum;
use App\Domain\User\Event\SendConfirmationEmailEvent;
use App\Domain\User\Service\UserMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class SendConfirmationEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserMailer $userMailer,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEventEnum::SEND_CONFIRMATION_EMAIL->value => 'sendConfirmationEmail',
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendConfirmationEmail(SendConfirmationEmailEvent $event): void
    {
        $pendingRegistration = $event->getPendingRegistration();

        $this->userMailer->sendConfirmationEmail($pendingRegistration);
    }
}
