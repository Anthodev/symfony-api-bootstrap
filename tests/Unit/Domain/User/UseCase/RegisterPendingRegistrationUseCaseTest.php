<?php

use App\Application\Common\Exception\ValidationException;
use App\Domain\User\Dto\RegisterPendingRegistrationInput;
use App\Domain\User\Entity\PendingRegistration;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\RoleRepository;
use App\Domain\User\Repository\PendingRegistrationRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\UseCase\RegisterPendingRegistrationUseCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Ulid;

use function Pest\Faker\fake;

beforeEach(function () {
    $this->pendingRegistrationRepository = $this->getContainer()->get(PendingRegistrationRepository::class);
    $this->userRepository = $this->getContainer()->get(UserRepository::class);
    $this->roleRepository = $this->getContainer()->get(RoleRepository::class);
    $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
});

it('can register a pending registration', function () {
    // Given
    $registerUserInput = new RegisterPendingRegistrationInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterPendingRegistrationUseCase(
        $this->pendingRegistrationRepository,
        $this->roleRepository,
        $this->eventDispatcher,
    );

    // When
    $registerUserUseCase->registerPendingRegistration($registerUserInput);

    // Then
    $pendingRegistration = $this->pendingRegistrationRepository->findOneBy(['email' => $registerUserInput->getEmail()]);

    expect($pendingRegistration)
        ->toBeInstanceOf(PendingRegistration::class)
        ->getId()
            ->not()->toBeNull()
            ->toBeInstanceOf(Ulid::class)
        ->getEmail()
            ->toBe($registerUserInput->getEmail())
        ->getUsername()
            ->toBe($registerUserInput->getUsername())
        ->getRole()->getCode()
            ->toBe(RoleCodeEnum::ROLE_USER->value)
        ->getPassword()
            ->not()->toBeNull()
            ->not()->toBe($registerUserInput->getPassword())
        ->getPlainPassword()->toBeNull()
        ->getCreatedAt()
            ->not()->toBeNull()
            ->toBeInstanceOf(DateTimeInterface::class)
        ->getUpdatedAt()
            ->not()->toBeNull()
            ->toBeInstanceOf(DateTimeInterface::class)
    ;
});

it('cannot register a pending registration with an invalid password', function () {
    // Given
    $registerUserInput = new RegisterPendingRegistrationInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(1, 5),
    );

    $registerUserUseCase = new RegisterPendingRegistrationUseCase(
        $this->pendingRegistrationRepository,
        $this->roleRepository,
        $this->eventDispatcher,
    );

    // When
    $registerUserUseCase->registerPendingRegistration($registerUserInput);
})->throws(ValidationException::class, 'Your password must be at least 12 characters long.');

it('cannot register a pending registration with an invalid email', function () {
    // Given
    $registerUserInput = new RegisterPendingRegistrationInput(
        email: fake()->word(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterPendingRegistrationUseCase(
        $this->pendingRegistrationRepository,
        $this->roleRepository,
        $this->eventDispatcher,
    );

    // When
    $registerUserUseCase->registerPendingRegistration($registerUserInput);
})->throws(ValidationException::class, 'This value is not a valid email address.');

it('cannot register a pending registration with an existing email', function () {
    // Given
    $registerUserInput = new RegisterPendingRegistrationInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterPendingRegistrationUseCase(
        $this->pendingRegistrationRepository,
        $this->roleRepository,
        $this->eventDispatcher,
    );

    $registerUserUseCase->registerPendingRegistration($registerUserInput);

    $registerUserInput = new RegisterPendingRegistrationInput(
        email: $registerUserInput->getEmail(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    // When
    $registerUserUseCase->registerPendingRegistration($registerUserInput);
})->throws(ValidationException::class, 'This email is already used.');

it('cannot register a pending registration with an existing username', function () {
    // Given
    $registerUserInput = new RegisterPendingRegistrationInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterPendingRegistrationUseCase(
        $this->pendingRegistrationRepository,
        $this->roleRepository,
        $this->eventDispatcher,
    );

    $registerUserUseCase->registerPendingRegistration($registerUserInput);

    $registerUserInput = new RegisterPendingRegistrationInput(
        email: fake()->email(),
        username: $registerUserInput->getUsername(),
        password: fake()->password(16),
    );

    // When
    $registerUserUseCase->registerPendingRegistration($registerUserInput);
})->throws(ValidationException::class, 'This username is already used.');

it('cannot register a pending regsitration with an existing email and username', function () {
    // Given
    $registerUserInput = new RegisterPendingRegistrationInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterPendingRegistrationUseCase(
        $this->pendingRegistrationRepository,
        $this->roleRepository,
        $this->eventDispatcher,
    );

    $registerUserUseCase->registerPendingRegistration($registerUserInput);

    $registerUserInput = new RegisterPendingRegistrationInput(
        email: $registerUserInput->getEmail(),
        username: $registerUserInput->getUsername(),
        password: fake()->password(16),
    );

    // When
    $registerUserUseCase->registerPendingRegistration($registerUserInput);
})->throws(ValidationException::class, 'This email is already used.');

it('cannot register a pending registration with an existing user email', function () {
    // Given
    $email = fake()->email();
    $username = fake()->userName();
    $password = fake()->password(16);

    $roleUser = $this->roleRepository->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);

    $user = UserFactory::makeUser(
        email: $email,
        username: $username,
        password: $password,
    );
    $user->setRole($roleUser);
    $this->userRepository->save($user);

    $registerUserInput = new RegisterPendingRegistrationInput(
        email: $email,
        username: $username,
        password: $password,
    );

    $registerUserUseCase = new RegisterPendingRegistrationUseCase(
        $this->pendingRegistrationRepository,
        $this->roleRepository,
        $this->eventDispatcher,
    );

    // When
    $registerUserUseCase->registerPendingRegistration($registerUserInput);
})->throws(ValidationException::class, 'This email is already used.');
