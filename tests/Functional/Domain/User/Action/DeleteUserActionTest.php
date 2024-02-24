<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Faker\fake;

it('can delete an user', function () {
    $this->makeUser();

    $email = fake()->email();

    $user = $this->makeUser(
        email: $email,
        username: fake()->userName(),
        password: fake()->password(),
    );

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    static::$client->request(HttpMethodEnum::DELETE->value, '/api/users/' . $user->getId()->toRfc4122());
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_NO_CONTENT);

    $entityManager = static::getContainer()->get(EntityManagerInterface::class);
    $userRepository = $entityManager->getRepository(User::class);
    $user = $userRepository->findOneBy(['email' => $email]);

    expect($user)->toBeNull();
});

it('cannot delete the user list if not authenticated', function () {
    $fakeUuid = fake()->uuid();

    static::$client->request(HttpMethodEnum::GET->value, '/api/users/' . $fakeUuid);
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":401,"message":"JWT Token not found"}');
});

it('cannot delete an user that does not exist', function () {
    $this->makeUser();

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    $fakeUuid = fake()->uuid();

    static::$client->request(HttpMethodEnum::DELETE->value, '/api/users/' . $fakeUuid);
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":404,"message":"Data not found with id ' . $fakeUuid . '"}');
});

it('cannot delete an user that is disabled', function () {
    $this->makeUser();

    $user = $this->makeUser(
        email: fake()->email(),
        username: fake()->userName(),
        password: fake()->password(),
        enabled: false,
    );

    $this->loginUser(self::DEFAULT_USER_EMAIL);

    static::$client->request(HttpMethodEnum::DELETE->value, '/api/users/' . $user->getId()->toRfc4122());
    $response = static::$client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND);
    expect($response->getContent())->toBeJson();
    expect($response->getContent(false))->toBe('{"code":404,"message":"Data not found with id ' . $user->getId()->toRfc4122() . '"}');
});
