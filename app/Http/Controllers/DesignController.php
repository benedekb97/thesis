<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entities\DesignInterface;
use App\Services\Repository\DesignRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DesignController extends Controller
{
    private DesignRepositoryInterface $designRepository;

    public function __construct(
        EntityManager $entityManager,
        ResponseFactory $responseFactory,
        DesignRepositoryInterface $designRepository
    ) {
        $this->designRepository = $designRepository;

        parent::__construct($entityManager, $responseFactory);
    }

    public function colors(int $design)
    {
        $design = $this->designRepository->find($design);

        if ($design === null) {
            abort(404);
        }

        return view(
            'pages.colors',
            [
                'design' => $design
            ]
        );
    }

    public function saveColors(Request $request, $designId): RedirectResponse
    {
        /** @var DesignInterface $design */
        $design = $this->designRepository->find($designId);

        if ($design === null) {
            abort(404);
        }

        $colorCount = count($design->getStitches());

        $colors = [];

        for ($i = 0; $i < $colorCount; $i++) {
            if (!$request->request->has(sprintf('color-%d', $i))) {
                abort(400);
            }

            $colors[$i] = $request->get(sprintf('color-%d', $i));
        }

        $design->setColors($colors);

        $background = $request->get('background');

        if (strtolower($background) === '#ffffff') {
            $design->setBackgroundColor(null);
        } else {
            $design->setBackgroundColor($this->hexToRGB($background));
        }


        $this->entityManager->persist($design);
        $this->entityManager->flush();

        return new RedirectResponse(route('design.colors', ['design' => $design->getId()]));
    }

    private function hexToRGB(string $hex): array
    {
        $rgb = [];

        if (substr($hex, 0, 1) === '#') {
            $hex = substr($hex, 1, 6);
        }

        $components = str_split($hex, 2);

        $rgb['red'] = hexdec($components[0]);
        $rgb['green'] = hexdec($components[1]);
        $rgb['blue'] = hexdec($components[2]);

        return $rgb;
    }
}
