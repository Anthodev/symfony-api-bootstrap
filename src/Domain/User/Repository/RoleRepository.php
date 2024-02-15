<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Application\Common\Repository\BaseEntityRepository;
use App\Domain\User\Entity\Role;
use Doctrine\Persistence\ManagerRegistry;

class RoleRepository extends BaseEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }
}
