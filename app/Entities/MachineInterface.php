<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\ResourceInterface;
use App\Entities\Traits\TimestampableInterface;

interface MachineInterface extends ResourceInterface, TimestampableInterface
{
    public const STATE_RUNNING = 'running';
    public const STATE_END = 'end';
    public const STATE_MACHINE_ERROR = 'machine_error';
    public const STATE_MANUAL_STOP = 'manual_stop';
    public const STATE_AUTOMATIC_STOP = 'automatic_stop';
    public const STATE_THREAD_BREAK = 'thread_break';

    public const PROGRESS_BAR_RUNNING = 'progress-bar-animated';
    public const PROGRESS_BAR_SUCCESS = 'bg-success';
    public const PROGRESS_BAR_DANGER = 'bg-danger';
    public const PROGRESS_BAR_WARNING = 'bg-warning';

    public const STOPPED_STATES = [
        self::STATE_END,
        self::STATE_MACHINE_ERROR,
        self::STATE_MANUAL_STOP,
        self::STATE_AUTOMATIC_STOP,
        self::STATE_THREAD_BREAK,
    ];

    public const RUNNING_STATES = [
        self::STATE_RUNNING,
    ];

    public const STATE_STATUS_MAP = [
        self::STATE_RUNNING => 'Fut',
        self::STATE_END => 'Vége',
        self::STATE_MACHINE_ERROR => 'Géphiba',
        self::STATE_MANUAL_STOP => 'Megállítva',
        self::STATE_AUTOMATIC_STOP => 'Előre beállított stop',
        self::STATE_THREAD_BREAK => 'Szálszakadás',
    ];

    public const STATE_PROGRESS_BAR_MAP = [
        self::STATE_RUNNING => self::PROGRESS_BAR_RUNNING,
        self::STATE_END => self::PROGRESS_BAR_SUCCESS,
        self::STATE_THREAD_BREAK => self::PROGRESS_BAR_DANGER,
        self::STATE_MACHINE_ERROR => self::PROGRESS_BAR_DANGER,
        self::STATE_AUTOMATIC_STOP => self::PROGRESS_BAR_WARNING,
        self::STATE_MANUAL_STOP => self::PROGRESS_BAR_WARNING,
    ];

    public const STATE_MACHINE_CODE_MAP = [
        0 => self::STATE_END,
        1 => self::STATE_RUNNING,
        2 => self::STATE_MACHINE_ERROR,
        3 => self::STATE_RUNNING,
        4 => self::STATE_MANUAL_STOP,
        5 => self::STATE_AUTOMATIC_STOP,
        6 => self::STATE_THREAD_BREAK,
    ];

    public function setDesign(?DesignInterface $design): void;

    public function getDesign(): ?DesignInterface;

    public function getState(): ?string;

    public function setState(?string $state): void;

    public function getStatus(): ?string;

    public function isActive(): bool;

    public function activate(): void;

    public function deactivate(): void;

    public function getCurrentStitch(): ?int;

    public function setCurrentStitch(?int $currentStitch): void;

    public function getSecondsRunning(): ?int;

    public function setSecondsRunning(?int $secondsRunning): void;

    public function getCurrentDesign(): ?int;

    public function setCurrentDesign(?int $currentDesign): void;

    public function getDesignCount(): ?int;

    public function setDesignCount(?int $designCount): void;

    public function isRunning(): bool;

    public function isStopped(): bool;

    public function getProgressBarStyle(): ?string;

    public function getStitchProgressBarPercentage(): ?float;

    public function getFinishedDesignsProgressBarPercentage(): float;

    public function getCurrentDesignProgressBarPercentage(): float;
}
