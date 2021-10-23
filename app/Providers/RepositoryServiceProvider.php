<?php

declare(strict_types=1);

namespace App\Providers;

use App\Entities\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private const ENTITY_REPOSITORY_MAP = [
        User::class => UserRepository::class,
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
