<?php

declare(strict_types=1);

namespace App\Services\Activator;

use App\Entities\MachineInterface;
use App\Services\Repository\MachineRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class MachineActivator implements MachineActivatorInterface
{
    private MachineRepositoryInterface $machineRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        MachineRepositoryInterface $machineRepository,
        EntityManager $entityManager
    ) {
        $this->machineRepository = $machineRepository;
        $this->entityManager = $entityManager;
    }

    public function activate(MachineInterface $machine): void
    {
        $this->deactivateMachines();

        $machine->activate();

        $this->entityManager->persist($machine);
        $this->entityManager->flush();
    }

    private function deactivateMachines(): void
    {
        $activeMachines = $this->machineRepository->findAllActive();

        /** @var MachineInterface $machine */
        foreach ($activeMachines as $machine) {
            $machine->deactivate();

            $this->entityManager->persist($machine);
        }
    }
}
