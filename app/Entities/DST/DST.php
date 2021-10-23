<?php

declare(strict_types=1);

namespace App\Entities\DST;

class DST implements DSTInterface
{
    private array $stitches = [];

    private ?int $colorCount = null;

    private ?int $stitchCount = null;

    private Position $maxPosition;

    private Position $minPosition;

    private Position $currentPosition;

    public function __construct()
    {
        $this->maxPosition = new Position();
        $this->minPosition = new Position();
        $this->currentPosition = new Position();
    }

    public function getStitches(): array
    {
        return $this->stitches;
    }

    public function setStitches(array $stitches): void
    {
        $this->stitches = $stitches;
    }

    public function getColorCount(): int
    {
        return $this->colorCount ?? count($this->stitches);
    }

    public function setColorCount(int $colorCount): void
    {
        $this->colorCount = $colorCount;
    }

    public function incrementColorCount(): void
    {
        if (isset($this->colorCount)) {
            $this->colorCount++;

            return;
        }

        $this->colorCount = 1;
    }

    public function getStitchCount(): int
    {
        if (isset($this->stitchCount)) {
            return $this->stitchCount;
        }

        $sum = 0;

        foreach ($this->stitches as $color) {
            $sum += $color;
        }

        return $sum;
    }

    public function setStitchCount(int $stitchCount): void
    {
        $this->stitchCount = $stitchCount;
    }

    public function incrementStitchCount(): void
    {
        if (isset($this->stitchCount)) {
            $this->stitchCount++;

            return;
        }

        $this->stitchCount = 1;
    }

    public function getMaxPosition(): Position
    {
        return $this->maxPosition;
    }

    public function setMaxPosition(Position $maxPosition): void
    {
        $this->maxPosition = $maxPosition;
    }

    public function getMinPosition(): Position
    {
        return $this->minPosition;
    }

    public function setMinPosition(Position $minPosition): void
    {
        $this->minPosition = $minPosition;
    }

    public function addStitchByNextPosition(Position $position): void
    {
        $this->stitches[$this->colorCount][] = [$this->currentPosition->toArray(), $position->toArray()];
    }

    public function getCanvasWidth(): float
    {
        return abs($this->maxPosition->getHorizontal()) + abs($this->minPosition->getHorizontal()) + 10;
    }

    public function getCanvasHeight(): float
    {
        return abs($this->maxPosition->getVertical()) + abs($this->minPosition->getVertical()) + 10;
    }

    public function getCurrentPosition(): Position
    {
        return $this->currentPosition;
    }

    public function setCurrentPosition(Position $currentPosition): void
    {
        $this->currentPosition = $currentPosition;
    }
}
