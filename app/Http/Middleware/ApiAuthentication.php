<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Repositories\UserRepositoryInterface;
use Closure;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    private const EXCEPT = [
        '/api/auth',
    ];

    private UserRepositoryInterface $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->getRequestUri(), self::EXCEPT, true)) {
            return $next($request);
        }

        $user = $this->userRepository->findOneByApiToken($request->headers->get('apiToken'));

        if ($user === null || new DateTime() > $user->getApiTokenExpiry()) {
            return new JsonResponse(
                [
                    'error' => 'Unauthenticated.',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
