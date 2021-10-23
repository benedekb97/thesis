<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\NameableTrait;
use App\Entities\Traits\ResourceTrait;
use App\Entities\Traits\TimestampableTrait;

class Design implements DesignInterface
{
    use ResourceTrait;
    use TimestampableTrait;
    use NameableTrait;

    private ?array $stitches = null;

    private ?string $file = null;

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
}
