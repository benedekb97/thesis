<?php

declare(strict_types=1);

namespace App\Services\Resolver;

use App\Entities\MachineInterface;
use Symfony\Component\HttpFoundation\Request;

interface ActiveMachineResolverInterface
{
    public function resolve(): ?MachineInterface;
}
