<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Application\Common\Repository\BaseEntityRepository;
use App\Domain\User\Entity\User;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends BaseEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmailOrUsername(string $email, string $username): ?User
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere('u.username = :username')
            ->setParameter('email', $email)
            ->setParameter('username', $username)
            ->getQuery();

        /** @var User|null */
        return $query->getOneOrNullResult();
    }
}
