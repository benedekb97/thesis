<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MachineDesignUpdateEvent implements ShouldBroadcast
{
    use Dispatchable;

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('machine-update');
    }
}
