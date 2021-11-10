<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\NameableTrait;
use App\Entities\Traits\ResourceTrait;
use App\Entities\Traits\TimestampableTrait;
use Illuminate\Support\Arr;

class Design implements DesignInterface
{
    use ResourceTrait;
    use TimestampableTrait;
    use NameableTrait;

    private ?array $stitches = null;

    private ?string $file = null;

    private ?string $svg = null;

    private ?array $backgroundColor = null;

    private ?array $colors = null;

    private ?float $canvasWidth = null;

    private ?float $canvasHeight = null;

    private ?float $horizontalOffset = null;

    private ?float $verticalOffset = null;

    public function getStitches(): ?array
    {
        return $this->stitches;
    }

    public function setStitches(?array $stitches): void
    {
        $this->stitches = $stitches;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    public function getSVG(): ?string
    {
        return $this->svg;
    }

    public function setSVG(?string $svg): void
    {
        $this->svg = $svg;
    }

    public function getStitchCount(): ?int
    {
        if (!isset($this->stitches)) {
            return null;
        }

        return array_sum(
            array_map(
                static function ($color) {
                    return count($color);
                },
                $this->stitches
            )
        );
    }

    public function getBackgroundColor(): ?array
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?array $backgroundColor): void
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function hasBackgroundColor(): bool
    {
        return isset($this->backgroundColor) &&
            array_key_exists('red', $this->backgroundColor) &&
            array_key_exists('green', $this->backgroundColor) &&
            array_key_exists('blue', $this->backgroundColor);
    }

    public function getHexBackgroundColor(): ?string
    {
        return $this->hasBackgroundColor()
            ? sprintf(
                '#%s%s%s',
                str_pad(dechex($this->backgroundColor['red']), 2, '0', STR_PAD_LEFT),
                str_pad(dechex($this->backgroundColor['green']), 2, '0', STR_PAD_LEFT),
                str_pad(dechex($this->backgroundColor['blue']), 2, '0', STR_PAD_LEFT)
            )
            : null;
    }

    public function getColors(): ?array
    {
        return $this->colors;
    }

    public function setColors(?array $colors): void
    {
        $this->colors = $colors;
    }

    public function hasColors(): bool
    {
        return isset($this->colors);
    }

    public function hasColor($colorId): bool
    {
        return $this->hasColors() && array_key_exists($colorId, $this->colors);
    }

    public function getCanvasWidth(): ?float
    {
        return $this->canvasWidth;
    }

    public function setCanvasWidth(?float $canvasWidth): void
    {
        $this->canvasWidth = $canvasWidth;
    }

    public function getCanvasHeight(): ?float
    {
        return $this->canvasHeight;
    }

    public function setCanvasHeight(?float $canvasHeight): void
    {
        $this->canvasHeight = $canvasHeight;
    }

    public function getHorizontalOffset(): ?float
    {
        return $this->horizontalOffset;
    }

    public function setHorizontalOffset(?float $horizontalOffset): void
    {
        $this->horizontalOffset = $horizontalOffset;
    }

    public function getVerticalOffset(): ?float
    {
        return $this->verticalOffset;
    }

    public function setVerticalOffset(?float $verticalOffset): void
    {
        $this->verticalOffset = $verticalOffset;
    }

    public function getSquashedStitches(): array
    {
        return Arr::flatten($this->stitches, 1);
    }
}
