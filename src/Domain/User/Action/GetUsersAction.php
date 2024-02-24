<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

use App\Application\Common\Action\BaseAction;
use App\Application\Common\Enum\HttpMethodEnum;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class GetUsersAction extends BaseAction
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route('/users', methods: [HttpMethodEnum::GET->value])]
    public function getUsers(): Response
    {
        $users = $this->userRepository->getAllEnabled();

        return $this->output($users);
    }
}
