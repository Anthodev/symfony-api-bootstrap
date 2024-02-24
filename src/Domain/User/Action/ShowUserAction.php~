<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

use App\Application\Common\Action\BaseAction;
use App\Application\Common\Enum\HttpMethodEnum;
use App\Application\Common\Exception\EntityNotFoundHttpException;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
readonly class ShowUserAction extends BaseAction
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/users/{id}', methods: [HttpMethodEnum::GET->value])]
    public function getUsers(string $id): Response
    {
        $user = $this->userRepository->getOneByIdEnabled($id);

        if (null === $user) {
            throw new EntityNotFoundHttpException($id);
        }

        return $this->output($user);
    }
}
