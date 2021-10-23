<?php

declare(strict_types=1);

namespace App\Http;

use App\Entities\MachineInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiRequest extends Request
{
    private ?MachineInterface $machine = null;

    public function setMachine(?MachineInterface $machine): void
    {
        $this->machine = $machine;
    }

    public function getMachine(): ?MachineInterface
    {
        return $this->machine;
    }
}
