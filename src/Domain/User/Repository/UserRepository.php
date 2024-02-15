<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Application\Common\Repository\BaseEntityRepository;
use App\Domain\User\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends BaseEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
