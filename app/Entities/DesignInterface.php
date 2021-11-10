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

    public function getStitchCount(): ?int;

    public function getBackgroundColor(): ?array;

    public function setBackgroundColor(?array $backgroundColor): void;

    public function hasBackgroundColor(): bool;

    public function getHexBackgroundColor(): ?string;

    public function getColors(): ?array;

    public function setColors(?array $colors): void;

    public function hasColors(): bool;

    public function hasColor($colorId): bool;

    public function getCanvasWidth(): ?float;

    public function setCanvasWidth(?float $canvasWidth): void;

    public function getCanvasHeight(): ?float;

    public function setCanvasHeight(?float $canvasHeight): void;

    public function getHorizontalOffset(): ?float;

    public function setHorizontalOffset(?float $horizontalOffset): void;

    public function getVerticalOffset(): ?float;

    public function setVerticalOffset(?float $verticalOffset): void;

    public function getSquashedStitches(): array;
}
