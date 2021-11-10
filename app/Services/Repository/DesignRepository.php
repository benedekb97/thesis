<?php

declare(strict_types=1);

namespace App\Services\Repository;

use App\Entities\DesignInterface;
use Doctrine\ORM\EntityRepository;

class DesignRepository extends EntityRepository implements DesignRepositoryInterface
{
    public function findByStitches(array $stitches): ?DesignInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.stitches = :stitches')
            ->setParameter('stitches', $stitches)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
