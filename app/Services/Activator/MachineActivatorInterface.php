<?php

declare(strict_types=1);

namespace App\Services\Activator;

use App\Entities\MachineInterface;

interface MachineActivatorInterface
{
    public function activate(MachineInterface $machine): void;
}
