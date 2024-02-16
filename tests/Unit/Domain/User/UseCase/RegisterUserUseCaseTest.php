<?php

use App\Application\Common\Exception\ValidationException;
use App\Domain\User\Dto\RegisterUserInput;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Repository\RoleRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\UseCase\RegisterUserUseCase;
use Symfony\Component\Uid\Ulid;

use function Pest\Faker\fake;

it('can register a user', function () {
    $userRepository = $this->getContainer()->get(UserRepository::class);

    $registerUserInput = new RegisterUserInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterUserUseCase(
        $userRepository,
        $this->getContainer()->get(RoleRepository::class),
    );

    $registerUserUseCase->registerUser($registerUserInput);

    $user = $userRepository->findOneBy(['email' => $registerUserInput->getEmail()]);

    expect($user)
        ->toBeInstanceOf(User::class)
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

it('cannot register a user with an invalid password', function () {
    $userRepository = $this->getContainer()->get(UserRepository::class);

    $registerUserInput = new RegisterUserInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(1, 5),
    );

    $registerUserUseCase = new RegisterUserUseCase(
        $userRepository,
        $this->getContainer()->get(RoleRepository::class),
    );

    $registerUserUseCase->registerUser($registerUserInput);
})->throws(ValidationException::class, 'Your password must be at least 12 characters long.');

it('cannot register a user with an invalid email', function () {
    $userRepository = $this->getContainer()->get(UserRepository::class);

    $registerUserInput = new RegisterUserInput(
        email: fake()->word(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterUserUseCase(
        $userRepository,
        $this->getContainer()->get(RoleRepository::class),
    );

    $registerUserUseCase->registerUser($registerUserInput);
})->throws(ValidationException::class, 'This value is not a valid email address.');

it('cannot register a user with an existing email', function () {
    $userRepository = $this->getContainer()->get(UserRepository::class);

    $registerUserInput = new RegisterUserInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterUserUseCase(
        $userRepository,
        $this->getContainer()->get(RoleRepository::class),
    );

    $registerUserUseCase->registerUser($registerUserInput);

    $registerUserInput = new RegisterUserInput(
        email: $registerUserInput->getEmail(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase->registerUser($registerUserInput);
})->throws(ValidationException::class, 'This email is already used.');

it('cannot register a user with an existing username', function () {
    $userRepository = $this->getContainer()->get(UserRepository::class);

    $registerUserInput = new RegisterUserInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterUserUseCase(
        $userRepository,
        $this->getContainer()->get(RoleRepository::class),
    );

    $registerUserUseCase->registerUser($registerUserInput);

    $registerUserInput = new RegisterUserInput(
        email: fake()->email(),
        username: $registerUserInput->getUsername(),
        password: fake()->password(16),
    );

    $registerUserUseCase->registerUser($registerUserInput);
})->throws(ValidationException::class, 'This username is already used.');

it('cannot register a user with an existing email and username', function () {
    $userRepository = $this->getContainer()->get(UserRepository::class);

    $registerUserInput = new RegisterUserInput(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(16),
    );

    $registerUserUseCase = new RegisterUserUseCase(
        $userRepository,
        $this->getContainer()->get(RoleRepository::class),
    );

    $registerUserUseCase->registerUser($registerUserInput);

    $registerUserInput = new RegisterUserInput(
        email: $registerUserInput->getEmail(),
        username: $registerUserInput->getUsername(),
        password: fake()->password(16),
    );

    $registerUserUseCase->registerUser($registerUserInput);
})->throws(ValidationException::class, 'This email is already used.');
