<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;

use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\RoleRepository;
use App\Domain\User\Repository\UserRepository;
use function Pest\Faker\fake;

it('can register a user', function () {
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(201);
});

it('cannot register a user with an invalid password', function () {
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => fake()->userName(),
            'password' => fake()->password(1, 5),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(422);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toBe('This value is too short. It should have 12 characters or more.');
});

it('cannot register a user with an invalid email', function () {
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => 'invalid-email',
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(422);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toBe('This value is not a valid email address.');
});

it('cannot register a user with an existing email', function () {
    $email = fake()->email();

    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => $email,
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(201);

    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => $email,
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(400);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toContain('This email is already used.');
});

it('cannot register a user with an existing username', function () {
    $username = fake()->userName();

    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => $username,
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(201);

    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => $username,
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(400);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toContain('This username is already used.');
});

it('cannot register a user with an invalid payload', function () {
    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => fake()->email(),
            'username' => fake()->userName(),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(422);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toContain('This value should be of type unknown.');
});

it('cannot register a pending registration with an existing user email', function () {
    $userRepository = $this->getContainer()->get(UserRepository::class);
    $roleRepository = $this->getContainer()->get(RoleRepository::class);

    $email = fake()->email();
    $username = fake()->userName();
    $password = fake()->password(16);

    $roleUser = $roleRepository->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);

    $user = UserFactory::makeUser(
        email: $email,
        username: $username,
        password: $password,
    );
    $user->setRole($roleUser);
    $userRepository->save($user);

    static::$client->request(
        HttpMethodEnum::POST->value,
        '/api/register',
        [
            'email' => $email,
            'username' => fake()->userName(),
            'password' => fake()->password(16),
        ]
    );

    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(400);

    json_validate($response->getContent());
    $data = json_decode($response->getContent(), true);
    expect($data['message'])->toContain('This email is already used.');
});
