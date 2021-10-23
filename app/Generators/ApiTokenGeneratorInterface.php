<?php

declare(strict_types=1);

namespace App\Generators;

use App\Entities\UserInterface;

interface ApiTokenGeneratorInterface
{
    public function generate(UserInterface $user): void;
}
