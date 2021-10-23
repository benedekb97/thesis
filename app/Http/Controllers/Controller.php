<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected EntityManagerInterface $entityManager;

    protected ResponseFactory $responseFactory;

    public function __construct(
        EntityManager $entityManager,
        ResponseFactory $responseFactory
    ) {
        $this->entityManager = $entityManager;
        $this->responseFactory = $responseFactory;
    }
}
