<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User\Service;

use App\Domain\User\Factory\PendingRegistrationFactory;
use App\Domain\User\Service\UserMailer;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;
use Twig\Environment;
use function Pest\Faker\fake;

it('should send a confirmation email', function () {
    $mailer = $this->createMock(MailerInterface::class);
    $twig = $this->createMock(Environment::class);

    $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
        fake()->email(),
        fake()->userName(),
        fake()->password(16),
    );

    $token = md5($pendingRegistration->getEmail());
    $pendingRegistration->setToken($token);

    $senderAddress = new Address('noreply@astral-planner.io', 'Astral Planner');
    $recepientAddress = new Address($pendingRegistration->getEmail(), $pendingRegistration->getUsername());
    $envelope = new Envelope($senderAddress, [$recepientAddress]);

    $message = new RawMessage(
        $twig->render(
            'email/confirm.html.twig',
            [
                'url' => sprintf('https://astral-planner.io/api/register/confirm/%s', $pendingRegistration->getToken()),
                'username' => $pendingRegistration->getUsername(),
            ]
        )
    );

    $mailer
        ->expects(self::once())
        ->method('send')
        ->with($message, $envelope)
    ;

    $userMail = new UserMailer(
        $mailer,
        $this->createMock(Environment::class),
    );

    $userMail->sendConfirmationEmail($pendingRegistration);
});

it('should have correct url in the email', function () {
    $mailer = $this->getContainer()->get(MailerInterface::class);
    $twig = $this->getContainer()->get(Environment::class);

    $email = fake()->email();

    $pendingRegistration = PendingRegistrationFactory::makePendingRegistration(
        $email,
        fake()->userName(),
        fake()->password(16),
    );

    $token = md5($pendingRegistration->getEmail());
    $pendingRegistration->setToken($token);

    $userMail = new UserMailer(
        $mailer,
        $twig,
    );

    $userMail->sendConfirmationEmail($pendingRegistration);

    $this->assertEmailCount(1);

    /** @var RawMessage $email */
    $emailContent = $this->getMailerMessage(0);
    expect($emailContent)
        ->toBeInstanceOf(RawMessage::class)
        ->and($emailContent->toString())
            ->toContain(sprintf('https://astral-planner.io/api/register/confirm/%s', $pendingRegistration->getToken()))
    ;
});
