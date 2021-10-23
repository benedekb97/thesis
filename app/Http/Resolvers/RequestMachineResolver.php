<?php

declare(strict_types=1);

namespace App\Http\Resolvers;

use App\Entities\MachineInterface;
use App\Repositories\MachineRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestMachineResolver implements RequestMachineResolverInterface
{
    private MachineRepositoryInterface $machineRepository;

    public function __construct(
        MachineRepositoryInterface $machineRepository
    ) {
        $this->machineRepository = $machineRepository;
    }

    public function resolve(Request $request): ?MachineInterface
    {
        if (!$request->headers->has('machineId')) {
            return null;
        }

        $machineId = $request->headers->get('machineId');

        return $this->machineRepository->find($machineId);
    }
}
