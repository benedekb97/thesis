<?php

declare(strict_types=1);

namespace App\Entities\DST;

interface DSTInterface
{
    public const COMMAND_LENGTH = 6;
    public const BYTE_LENGTH = 2;

    public const STITCH_TYPE_NORMAL = 'normal';
    public const STITCH_TYPE_COLOR_CHANGE = 'color_change';
    public const STITCH_TYPE_JUMP = 'jump';
    public const STITCH_TYPE_OTHER = 'other';

    public function getStitches(): array;

    public function setStitches(array $stitches): void;

    public function getColorCount(): int;

    public function setColorCount(int $colorCount): void;

    public function incrementColorCount(): void;

    public function getStitchCount(): int;

    public function setStitchCount(int $stitchCount): void;

    public function incrementStitchCount(): void;

    public function getMaxPosition(): Position;

    public function setMaxPosition(Position $maxPosition): void;

    public function getMinPosition(): Position;

    public function setMinPosition(Position $minPosition): void;

    public function addStitchByNextPosition(Position $position): void;

    public function getCanvasWidth(): float;

    public function getCanvasHeight(): float;

    public function getCurrentPosition(): Position;

    public function setCurrentPosition(Position $currentPosition): void;
}
