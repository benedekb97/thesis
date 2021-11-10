<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Entities\DesignInterface;
use App\Entities\MachineInterface;
use App\Events\MachineDesignUpdateEvent;
use App\Events\MachineStatusUpdateEvent;
use App\Services\Factory\DesignFactory;
use App\Services\Factory\DesignFactoryInterface;
use App\Services\Generator\DST\SVGGenerator;
use App\Services\Generator\DST\SVGGeneratorInterface;
use App\Http\Controllers\Controller;
use App\Services\Repository\DesignRepositoryInterface;
use App\Services\Resolver\ActiveMachineResolver;
use App\Services\Resolver\ActiveMachineResolverInterface;
use App\Services\Parser\DSTParser;
use App\Services\Parser\DSTParserInterface;
use App\Services\Repository\MachineRepositoryInterface;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Illuminate\Http\Request as UploadRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
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

    private DesignRepositoryInterface $designRepository;

    public function __construct(
        EntityManager              $entityManager,
        MachineRepositoryInterface $machineRepository,
        ResponseFactory            $responseFactory,
        ActiveMachineResolver      $activeMachineResolver,
        DesignFactory              $designFactory,
        DSTParser                  $dstParser,
        SVGGenerator               $svgGenerator,
        DesignRepositoryInterface  $designRepository
    )
    {
        $this->machineRepository = $machineRepository;
        $this->activeMachineResolver = $activeMachineResolver;
        $this->designFactory = $designFactory;
        $this->dstParser = $dstParser;
        $this->svgGenerator = $svgGenerator;
        $this->designRepository = $designRepository;

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

        if ($machine->getDesign() === null) {
            return new JsonResponse(
                [
                    'error' => 'No design loaded in machine.',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $state = (int)$request->get('state');
        $currentStitch = min($request->get('currentStitch'), $machine->getDesign()->getStitchCount());
        $designCount = max($request->get('designCount'), 1);
        $currentDesign = max(min($request->get('currentDesign'), $designCount), 1);

        if (!array_key_exists($state, MachineInterface::STATE_MACHINE_CODE_MAP)) {
            return new JsonResponse(
                [
                    'error' => sprintf('Unknown machine state \'%d\'', $state)
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $stopped = $machine->isStopped();

        $machine->setState(MachineInterface::STATE_MACHINE_CODE_MAP[$state]);
        $machine->setCurrentStitch($currentStitch);
        $machine->setDesignCount($designCount);
        $machine->setCurrentDesign($currentDesign);

        $running = $machine->isRunning();

        if ($stopped && $running) {
            $machine->setSecondsRunning(0);
        }

        if (!$stopped && $running) {
            $timeDifference = (new DateTime())->diff($machine->getUpdatedAt() ?? new DateTime());

            $machine->setSecondsRunning(
                $machine->getSecondsRunning() + (int)$timeDifference->format('s')
            );
        }

        if (!$running) {
            $machine->setSecondsRunning(0);
        }

        $this->entityManager->persist($machine);
        $this->entityManager->flush();

        MachineStatusUpdateEvent::dispatch($machine);

        return new JsonResponse(
            [],
            Response::HTTP_NO_CONTENT
        );
    }

    public function design(UploadRequest $request): JsonResponse
    {
        $machine = $this->activeMachineResolver->resolve();

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

        $filePath = sprintf('designs/%s', $fileName);

        $dst = $this->dstParser->parse($filePath);

        $designs = $this->designRepository->findAll();

        $design = Arr::first(
            array_filter(
                $designs,
                static function (DesignInterface $design) use ($dst): bool
                {
                    return json_encode($design->getStitches()) === json_encode($dst->getStitches());
                }
            )
        );

        if ($design === null) {
            $design = $this->designFactory->createNew();

            $design->setName('Machine design');
            $design->setStitches($dst->getStitches());
        }

        $design->setFile($filePath);

        $design->setSVG(
            $this->svgGenerator->generate($design, $dst)
        );

        $design->setCanvasHeight($dst->getCanvasHeight());
        $design->setCanvasWidth($dst->getCanvasWidth());

        $design->setHorizontalOffset(($minPosition = $dst->getMinPosition())->getHorizontal());
        $design->setVerticalOffset($minPosition->getVertical());

        $machine->setDesign($design);

        $machine->setCurrentStitch(0);
        $machine->setSecondsRunning(0);
        $machine->setCurrentDesign(1);
        $machine->setDesignCount(1);

        $this->entityManager->persist($design);
        $this->entityManager->persist($machine);
        $this->entityManager->flush();

        MachineDesignUpdateEvent::dispatch();

        return new JsonResponse(
            [],
            Response::HTTP_NO_CONTENT
        );
    }
}
