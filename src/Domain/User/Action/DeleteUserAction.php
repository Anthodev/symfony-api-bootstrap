<?php

declare(strict_types=1);

namespace App\Domain\User\Action;

use App\Application\Common\Action\BaseAction;
use App\Application\Common\Enum\HttpMethodEnum;
use App\Application\Common\Exception\EntityNotFoundHttpException;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Security\UserVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class DeleteUserAction extends BaseAction
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route('/users/{id}', methods: [HttpMethodEnum::DELETE->value])]
    public function getUsers(string $id): Response
    {
        $user = $this->userRepository->getOneByIdEnabled($id);

        if (null === $user) {
            throw new EntityNotFoundHttpException($id);
        }

        $this->denyAccessUnlessGranted(UserVoter::DELETE, $user);

        $this->userRepository->delete($user);

        return $this->output(status: Response::HTTP_NO_CONTENT);
    }
}
