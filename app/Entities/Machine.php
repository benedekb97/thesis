<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\ResourceTrait;
use App\Entities\Traits\TimestampableTrait;
use DateTimeInterface;
use LogicException;

class Machine implements MachineInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    private ?DesignInterface $design = null;

    private ?string $state = null;

    private bool $active = false;

    private ?int $currentStitch = null;

    private ?int $secondsRunning = null;

    public function getDesign(): ?DesignInterface
    {
        return $this->design;
    }

    public function setDesign(?DesignInterface $design): void
    {
        $this->design = $design;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        if (!in_array($state, self::STATE_MACHINE_CODE_MAP, true)) {
            throw new LogicException(
                sprintf('Unknown machine state \'%s\'!', $state)
            );
        }

        $this->state = $state;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function getCurrentStitch(): ?int
    {
        return $this->currentStitch;
    }

    public function setCurrentStitch(?int $currentStitch): void
    {
        $this->currentStitch = $currentStitch;
    }

    public function getSecondsRunning(): ?int
    {
        return $this->secondsRunning;
    }

    public function setSecondsRunning(?int $secondsRunning): void
    {
        $this->secondsRunning = $secondsRunning;
    }
}
