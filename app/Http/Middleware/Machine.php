<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\ApiRequest;
use App\Repositories\MachineRepositoryInterface;
use Closure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Machine
{
    public MachineRepositoryInterface $machineRepository;

    public function __construct(
        MachineRepositoryInterface $machineRepository
    ) {
        $this->machineRepository = $machineRepository;
    }

    public function handle(ApiRequest $request, Closure $next)
    {
        if ($request->headers->has('machineId')) {
            $machine = $this->machineRepository->find($request->headers->get('machineId'));

            $request->setMachine($machine);
        }

        if ($request->getMachine() === null) {
            return new JsonResponse(
                [
                    'error' => 'Invalid machine ID',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $next($request);
    }
}
