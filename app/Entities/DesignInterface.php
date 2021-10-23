<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\NameableInterface;
use App\Entities\Traits\ResourceInterface;
use App\Entities\Traits\TimestampableInterface;

interface DesignInterface extends
    ResourceInterface,
    TimestampableInterface,
    NameableInterface
{
    public function getStitches(): ?array;

    public function setStitches(?array $stitches): void;

    public function getFile(): ?string;

    public function setFile(?string $file): void;

    public function getSVG(): ?string;

    public function setSVG(?string $svg): void;
}
