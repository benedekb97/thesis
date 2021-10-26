<?php

declare(strict_types=1);

namespace App\Services\Resolver;

use App\Entities\MachineInterface;
use App\Services\Repository\MachineRepositoryInterface;
use Illuminate\Support\Arr;

class ActiveMachineResolver implements ActiveMachineResolverInterface
{
    private MachineRepositoryInterface $machineRepository;

    public function __construct(
        MachineRepositoryInterface $machineRepository
    ) {
        $this->machineRepository = $machineRepository;
    }

    public function resolve(): ?MachineInterface
    {
        $machines = $this->machineRepository->findAllActive();

        return Arr::first($machines);
    }
}
