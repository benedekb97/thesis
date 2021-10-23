<?php

declare(strict_types=1);

namespace App\Http\Resolvers;

use App\Entities\MachineInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestMachineResolverInterface
{
    public function resolve(Request $request): ?MachineInterface;
}
