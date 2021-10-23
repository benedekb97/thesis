<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entities\Machine;
use App\Entities\MachineInterface;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $machine = new Machine();

        $machine->setState(MachineInterface::STATE_END);

        $this->entityManager->persist($machine);
        $this->entityManager->flush();

        return view('pages.index');
    }
}
