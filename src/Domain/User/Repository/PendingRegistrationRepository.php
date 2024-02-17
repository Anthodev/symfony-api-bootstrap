<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Application\Common\Repository\BaseEntityRepository;
use App\Domain\User\Entity\PendingRegistration;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class PendingRegistrationRepository extends BaseEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PendingRegistration::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmailOrUsername(string $email, string $username): ?PendingRegistration
    {
        $query = $this->createQueryBuilder('pr')
            ->where('pr.email = :email')
            ->orWhere('pr.username = :username')
            ->setParameter('email', $email)
            ->setParameter('username', $username)
            ->getQuery();

        /** @var PendingRegistration|null */
        return $query->getOneOrNullResult();
    }
}
