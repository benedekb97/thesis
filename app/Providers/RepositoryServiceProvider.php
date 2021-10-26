<?php

declare(strict_types=1);

namespace App\Providers;

use App\Entities\Machine;
use App\Entities\User;
use App\Services\Repository\MachineRepository;
use App\Services\Repository\UserRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private const ENTITY_REPOSITORY_MAP = [
        User::class => UserRepository::class,
        Machine::class => MachineRepository::class,
    ];

    public function register(): void
    {
        foreach (self::ENTITY_REPOSITORY_MAP as $entityClass => $repositoryClass) {
            $this->app->bind(
                $repositoryClass,
                function (Application $application) use ($entityClass, $repositoryClass) {
                    return new $repositoryClass(
                        $application['em'],
                        $application['em']->getClassMetaData($entityClass)
                    );
                }
            );
        }

        foreach (self::ENTITY_REPOSITORY_MAP as $entityClass => $repositoryClass) {
            $this->app->bind(
                $repositoryClass . 'Interface',
                function (Application $application) use ($entityClass, $repositoryClass) {
                    return new $repositoryClass(
                        $application['em'],
                        $application['em']->getClassMetaData($entityClass)
                    );
                }
            );
        }
    }
}
