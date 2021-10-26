<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\MachineInterface;
use App\Services\Factory\DesignFactory;
use App\Services\Factory\DesignFactoryInterface;
use App\Services\Generator\DST\SVGGenerator;
use App\Services\Generator\DST\SVGGeneratorInterface;
use App\Http\Controllers\Controller;
use App\Services\Resolver\ActiveMachineResolver;
use App\Services\Resolver\ActiveMachineResolverInterface;
use App\Services\Parser\DSTParser;
use App\Services\Parser\DSTParserInterface;
use App\Services\Repository\MachineRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Illuminate\Http\Request as UploadRequest;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MachineController extends Controller
{
    private MachineRepositoryInterface $machineRepository;

    private ActiveMachineResolverInterface $activeMachineResolver;

    private DesignFactoryInterface $designFactory;

    private DSTParserInterface $dstParser;

    private SVGGeneratorInterface $svgGenerator;

    public function __construct(
        EntityManager              $entityManager,
        MachineRepositoryInterface $machineRepository,
        ResponseFactory            $responseFactory,
        ActiveMachineResolver      $activeMachineResolver,
        DesignFactory              $designFactory,
        DSTParser                  $dstParser,
        SVGGenerator               $svgGenerator
    )
    {
        $this->machineRepository = $machineRepository;
        $this->activeMachineResolver = $activeMachineResolver;
        $this->designFactory = $designFactory;
        $this->dstParser = $dstParser;
        $this->svgGenerator = $svgGenerator;

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
        $machine = $this->activeMachineResolver->resolve();

        if ($machine === null) {
            return new JsonResponse(
                [
                    'error' => 'No active machines.',
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
        $machine = $this->activeMachineResolver->resolve($request);

        if ($machine === null) {
            return new JsonResponse(
                [
                    'error' => 'No active machine.',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$request->files->has('dst')) {
            return new JsonResponse(
                [
                    'error' => 'No DST uploaded.',
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

        $design->setSVG(
            $this->svgGenerator->generate($design, $dst)
        );

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
