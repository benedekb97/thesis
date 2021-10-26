<?php

declare(strict_types=1);

namespace App\Services\Generator\DST;

use App\Entities\DesignInterface;
use App\Entities\DST\DSTInterface;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SVGGenerator implements SVGGeneratorInterface
{
    private FilesystemManager $filesystemManager;

    public function __construct(
        FilesystemManager $filesystemManager
    ) {
        $this->filesystemManager = $filesystemManager;
    }

    public function generate(DesignInterface $design, DSTInterface $dst): string
    {
        $view = view(
            self::SVG_VIEW,
            [
                'design' => $design,
                'stitches' => $dst->getStitches(),
                'x_offset' => $dst->getMinPosition()->getHorizontal(),
                'y_offset' => $dst->getMinPosition()->getVertical(),
                'width' => $dst->getCanvasWidth(),
                'height' => $dst->getCanvasHeight(),
            ]
        );

        try {
            $svg = $view->render();
        } catch (\Throwable $exception) {
            abort(Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }

        $name = sprintf('%s%s.svg', time(), Str::random());

        $this->filesystemManager->put(sprintf('public/images/svg/%s', $name), $svg);

        return $name;
    }
}
