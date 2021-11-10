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

    private ?int $currentDesign = null;

    private ?int $designCount = null;

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

    public function getStatus(): ?string
    {
        return isset($this->state)
            ? self::STATE_STATUS_MAP[$this->state]
            : null;
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

    public function getCurrentDesign(): ?int
    {
        return $this->currentDesign;
    }

    public function setCurrentDesign(?int $currentDesign): void
    {
        $this->currentDesign = $currentDesign;
    }

    public function getDesignCount(): ?int
    {
        return $this->designCount;
    }

    public function setDesignCount(?int $designCount): void
    {
        $this->designCount = $designCount;
    }

    public function isRunning(): bool
    {
        return in_array($this->state, self::RUNNING_STATES);
    }

    public function isStopped(): bool
    {
        return in_array($this->state, self::STOPPED_STATES);
    }

    public function getProgressBarStyle(): ?string
    {
        return isset($this->state)
            ? self::STATE_PROGRESS_BAR_MAP[$this->state]
            : null;
    }

    public function getStitchProgressBarPercentage(): ?float
    {
        return isset($this->design)
            ? ($this->currentStitch / $this->design->getStitchCount()) * 100
            : 0;
    }

    public function getFinishedDesignsProgressBarPercentage(): float
    {
        return (($this->currentDesign - 1) / $this->designCount) * 100 ?? 100.0;
    }

    public function getCurrentDesignProgressBarPercentage(): float
    {
        return (1 / $this->designCount) * 100 ?? 0.0;
    }
}
