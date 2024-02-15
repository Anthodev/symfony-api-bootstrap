<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

use App\Application\Common\Enum\HttpMethodEnum;
use App\Application\Common\Exception\BadRequestHttpException;
use App\Application\Common\Exception\ValidationException;
use App\Application\Common\Exception\ValidationHttpException;
use App\Domain\User\Dto\RegisterUserInput;
use App\Domain\User\UseCase\RegisterUserUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
readonly class RegisterAction
{
    public function __construct(
        private RegisterUserUseCase $registerUserUseCase,
    ) {
    }

    #[Route('/register', name: 'register', methods: [HttpMethodEnum::POST->value])]
    public function register(
        #[MapRequestPayload] RegisterUserInput $registerUserInput,
    ): Response {
        try {
            $this->registerUserUseCase->registerUser($registerUserInput);
        } catch (ValidationException $e) {
            throw new ValidationHttpException($e->getMessage());
        } catch (\Exception) {
            throw new BadRequestHttpException('User registration failed.');
        }

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
