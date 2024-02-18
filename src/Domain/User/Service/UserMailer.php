<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\PendingRegistration;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendConfirmationEmail(PendingRegistration $pendingRegistration): void
    {
        $senderAddress = new Address('noreply@astral-planner.io', 'Astral Planner');
        $recepientAddress = new Address($pendingRegistration->getEmail(), $pendingRegistration->getUsername());
        $envelope = new Envelope($senderAddress, [$recepientAddress]);

        $message = new RawMessage(
            $this->twig->render(
                'email/confirm.html.twig',
                [
                    'url' => sprintf('https://astral-planner.io/api/register/confirm/%s', $pendingRegistration->getToken()),
                    'username' => $pendingRegistration->getUsername(),
                ]
            )
        );

        $this->mailer->send(
            $message,
            $envelope
        );
    }
}
