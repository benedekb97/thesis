<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\MachineInterface;
use App\Factories\DesignFactory;
use App\Factories\DesignFactoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resolvers\RequestMachineResolver;
use App\Http\Resolvers\RequestMachineResolverInterface;
use App\Parser\DSTParser;
use App\Parser\DSTParserInterface;
use App\Repositories\MachineRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Illuminate\Http\Request as UploadRequest;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\String\b;

class MachineController extends Controller
{
    private MachineRepositoryInterface $machineRepository;

    private RequestMachineResolverInterface $requestMachineResolver;

    private DesignFactoryInterface $designFactory;

    private DSTParserInterface $dstParser;

    public function __construct(
        EntityManager              $entityManager,
        MachineRepositoryInterface $machineRepository,
        ResponseFactory            $responseFactory,
        RequestMachineResolver     $requestMachineResolver,
        DesignFactory              $designFactory,
        DSTParser                  $dstParser
    )
    {
        $this->machineRepository = $machineRepository;
        $this->requestMachineResolver = $requestMachineResolver;
        $this->designFactory = $designFactory;
        $this->dstParser = $dstParser;

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

    public function design(UploadRequest $request): JsonResponse
    {
        $machine = $this->requestMachineResolver->resolve($request);

        if ($machine === null) {
            return new JsonResponse(
                [
                    'error' => 'Invalid machine ID',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$request->files->has('dst')) {
            return new JsonResponse(
                [
                    'error' => 'No DST uploaded',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        /** @var UploadedFile $dst */
        $dst = $request->file('dst');

        $dst->storeAs('designs', $fileName = (date('YmdHis') . '.dst'));

        $design = $this->designFactory->createNew();

        $design->setName('Machine design');
        $design->setFile(
            sprintf('designs/%s', $fileName)
        );

        $dst = $this->dstParser->parse($design->getFile());

        $design->setStitches($dst->getStitches());

        $machine->setDesign($design);

        $this->entityManager->persist($design);
        $this->entityManager->persist($machine);
        $this->entityManager->flush();

        return new JsonResponse(
            [],
            Response::HTTP_NO_CONTENT
        );
    }
}
