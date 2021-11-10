<?php

declare(strict_types=1);

namespace App\Services\Repository;

use App\Entities\DesignInterface;
use Doctrine\Persistence\ObjectRepository;

interface DesignRepositoryInterface extends ObjectRepository
{
    public function findByStitches(array $stitches): ?DesignInterface;
}
