<?php

declare(strict_types=1);

namespace App\Services\Repository;

use App\Entities\UserInterface;

interface UserRepositoryInterface extends EntityRepositoryInterface
{
    public function findOneByEmail(string $email): ?UserInterface;

    public function findOneByApiToken(string $apiToken): ?UserInterface;
}
