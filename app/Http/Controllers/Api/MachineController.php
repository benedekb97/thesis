<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\MachineInterface;
use App\Http\ApiRequest;
use App\Http\Controllers\Controller;
use App\Repositories\MachineRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MachineController extends Controller
{
    private MachineRepositoryInterface $machineRepository;

    public function __construct(
        EntityManager $entityManager,
        MachineRepositoryInterface $machineRepository
    )
    {
        $this->machineRepository = $machineRepository;

        parent::__construct($entityManager);
    }

    public function index(): JsonResponse
    {
        $machines = $this->machineRepository->findAll();

        return new JsonResponse(
            [
                'machines' => $machines,
                'total' => count($machines),
            ]
        );
    }

    public function status(ApiRequest $request): JsonResponse
    {
        /** @var MachineInterface $machine */
        $machine = $request->getMachine();

        $state = (int)$request->get('state');

        if (!array_key_exists($state, MachineInterface::STATE_MACHINE_CODE_MAP)) {
            return new JsonResponse(
                [
                    'error' => sprintf('Unknown machine state \'%d\'', $state)
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $machine->setState(
            MachineInterface::STATE_MACHINE_CODE_MAP[$state]
        );

        $this->entityManager->persist($machine);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'success' => true,
            ]
        );
    }
}
