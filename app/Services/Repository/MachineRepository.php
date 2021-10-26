<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Doctrine\ORM\EntityRepository;

class MachineRepository extends EntityRepository implements MachineRepositoryInterface
{
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.active = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }
}
