<?php

declare(strict_types=1);

namespace App\Services\Generator\DST;

use App\Entities\DesignInterface;
use App\Entities\DST\DSTInterface;

interface SVGGeneratorInterface
{
    public const SVG_VIEW = 'designs.svg';

    public function generate(DesignInterface $design, DSTInterface $dst): string;
}
