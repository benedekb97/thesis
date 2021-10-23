<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\MachineInterface;
use App\Http\ApiRequest;
use App\Http\Controllers\Controller;
use App\Http\Resolvers\RequestMachineResolver;
use App\Http\Resolvers\RequestMachineResolverInterface;
use App\Repositories\MachineRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MachineController extends Controller
{
    private MachineRepositoryInterface $machineRepository;

    private RequestMachineResolverInterface $requestMachineResolver;

    public function __construct(
        EntityManager $entityManager,
        MachineRepositoryInterface $machineRepository,
        ResponseFactory $responseFactory,
        RequestMachineResolver $requestMachineResolver
    )
    {
        $this->machineRepository = $machineRepository;
        $this->requestMachineResolver = $requestMachineResolver;

        parent::__construct($entityManager, $responseFactory);
    }

    public function index(): Response
    {
        $machines = $this->machineRepository->findAllActive();

        return $this->responseFactory->createFromArray(
            [
                'machines' => $machines,
                'total' => count($machines),
            ]
        );
    }

    public function status(Request $request): JsonResponse
    {
        $machine = $this->requestMachineResolver->resolve($request);

        if ($machine === null) {
            return new JsonResponse(
                [
                    'error' => 'Invalid machine ID.',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $state = (int)$request->get('state');
        $currentStitch = $request->get('currentStitch');

        if (!array_key_exists($state, MachineInterface::STATE_MACHINE_CODE_MAP)) {
            return new JsonResponse(
                [
                    'error' => sprintf('Unknown machine state \'%d\'', $state)
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $machine->setState(MachineInterface::STATE_MACHINE_CODE_MAP[$state]);
        $machine->setCurrentStitch($currentStitch);

        $this->entityManager->persist($machine);
        $this->entityManager->flush();

        return new JsonResponse(
            [],
            Response::HTTP_NO_CONTENT
        );
    }
}
