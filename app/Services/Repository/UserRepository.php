<?php

declare(strict_types=1);

namespace App\Services\Repository;

use App\Entities\UserInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function findOneByEmail(string $email): ?UserInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByApiToken(string $apiToken): ?UserInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.apiToken = :apiToken')
            ->setParameter('apiToken', $apiToken)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
