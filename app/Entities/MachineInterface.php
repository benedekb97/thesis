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

    public const STATE_MACHINE_CODE_MAP = [
        0 => self::STATE_END,
        1 => self::STATE_RUNNING,
        2 => self::STATE_MACHINE_ERROR,
        3 => self::STATE_RUNNING,
        4 => self::STATE_MANUAL_STOP,
        5 => self::STATE_AUTOMATIC_STOP,
        6 => self::STATE_THREAD_BREAK,
    ];

    public function getState(): ?string;

    public function setState(?string $state): void;

    public function isActive(): bool;

    public function activate(): void;

    public function deactivate(): void;

    public function getCurrentStitch(): ?int;

    public function setCurrentStitch(?int $currentStitch): void;
}
