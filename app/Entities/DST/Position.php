<?php

declare(strict_types=1);

namespace App\Entities\DST;

class Position
{
    private float $horizontal;

    private float $vertical;

    public function __construct(float $horizontal = 0.0, float $vertical = 0.0)
    {
        $this->horizontal = $horizontal;
        $this->vertical = $vertical;
    }

    public function getHorizontal(): float
    {
        return $this->horizontal;
    }

    public function setHorizontal(float $horizontal): void
    {
        $this->horizontal = $horizontal;
    }

    public function getVertical(): float
    {
        return $this->vertical;
    }

    public function setVertical(float $vertical): void
    {
        $this->vertical = $vertical;
    }

    public function toArray(): array
    {
        return [$this->horizontal, $this->vertical];
    }
}
