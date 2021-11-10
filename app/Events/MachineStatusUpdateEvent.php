<?php

declare(strict_types=1);

namespace App\Events;

use App\Entities\MachineInterface;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MachineStatusUpdateEvent implements ShouldBroadcast
{
    use Dispatchable;

    public int $machineId;

    public string $state;

    public ?string $status;

    public int $secondsRunning;

    public int $currentStitch;

    public int $currentDesign;

    public int $designCount;

    public array $crosshairPosition;

    public string $progressBarStyle;

    public float $stitchProgressBarPercentage;

    public float $finishedDesignsProgressBarPercentage;

    public float $currentDesignProgressBarPercentage;

    public function __construct(
        MachineInterface $machine
    ) {
        $design = $machine->getDesign();

        $this->machineId = $machine->getId();
        $this->state = $machine->getState();
        $this->secondsRunning = $machine->getSecondsRunning() ?? 0;
        $this->currentStitch = $currentStitch = $machine->getCurrentStitch() ?? 0;
        $this->currentDesign = $machine->getCurrentDesign() ?? 0;
        $this->designCount = $machine->getDesignCount() ?? 0;
        $this->status = $machine->getStatus();
        $this->progressBarStyle = $machine->getProgressBarStyle();
        $this->stitchProgressBarPercentage = round($machine->getStitchProgressBarPercentage(), 2);
        $this->finishedDesignsProgressBarPercentage = $machine->getFinishedDesignsProgressBarPercentage();
        $this->currentDesignProgressBarPercentage = $machine->getCurrentDesignProgressBarPercentage();

        $stitches = array_values($design->getSquashedStitches());

        if (array_key_exists($currentStitch ?? 0, $stitches)) {
            $this->crosshairPosition = [
                'horizontal' => $stitches[$currentStitch][0][0] + abs($design->getHorizontalOffset()) + 5,
                'vertical' => $stitches[$currentStitch][0][1] + abs($design->getVerticalOffset()) + 5,
            ];
        } else {
            $this->crosshairPosition = [
                'horizontal' => $stitches[0][0][0] + abs($design->getHorizontalOffset()) + 5,
                'vertical' => $stitches[0][0][1] + abs($design->getVerticalOffset()) + 5,
            ];
        }
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('machine-update');
    }
}
