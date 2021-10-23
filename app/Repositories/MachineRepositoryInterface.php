<?php

declare(strict_types=1);

namespace App\Repositories;

use Doctrine\Persistence\ObjectRepository;

interface MachineRepositoryInterface extends ObjectRepository
{
    public function findAllActive(): array;
}
