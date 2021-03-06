<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\Generator\ApiTokenGenerator;
use App\Services\Generator\ApiTokenGeneratorInterface;
use App\Http\Controllers\Controller;
use App\Services\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Illuminate\Hashing\HashManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    private UserRepositoryInterface $userRepository;

    private HashManager $hashManager;

    private ApiTokenGeneratorInterface $apiTokenGenerator;

    public function __construct(
        EntityManager $entityManager,
        ResponseFactory $responseFactory,
        UserRepositoryInterface $userRepository,
        HashManager $hashManager,
        ApiTokenGenerator $apiTokenGenerator
    ) {
        $this->userRepository = $userRepository;
        $this->hashManager = $hashManager;
        $this->apiTokenGenerator = $apiTokenGenerator;

        parent::__construct($entityManager, $responseFactory);
    }

    public function authenticate(Request $request): JsonResponse
    {
        $user = $this->userRepository->findOneByEmail($request->get('email'));

        if ($user->getPassword() === null) {
            return new JsonResponse(
                [
                    'error' => 'Please set your password on your profile page!',
                    'link' => route('profile'),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $password = $request->get('password');

        if ($this->hashManager->check($password, $user->getPassword())) {
            $this->apiTokenGenerator->generate($user);
        } else {
            return new JsonResponse(
                [
                    'error' => 'Incorrect credentials',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'token' => $user->getApiToken(),
                'expires_at' => $user->getApiTokenExpiry()->format('Y-m-d H:i:s'),
            ],
            Response::HTTP_OK
        );
    }
}
