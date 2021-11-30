<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entities\UserInterface;
use App\Services\Resolver\ActiveMachineResolver;
use App\Services\Resolver\ActiveMachineResolverInterface;
use Doctrine\ORM\EntityManager;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Illuminate\Auth\AuthManager;
use Illuminate\Hashing\HashManager;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    private AuthManager $authManager;

    private HashManager $hashManager;

    private ActiveMachineResolverInterface $activeMachineResolver;

    public function __construct(
        EntityManager $entityManager,
        ResponseFactory $responseFactory,
        AuthManager $authManager,
        HashManager $hashManager,
        ActiveMachineResolver $activeMachineResolver
    )
    {
        $this->authManager = $authManager;
        $this->hashManager = $hashManager;
        $this->activeMachineResolver = $activeMachineResolver;

        parent::__construct($entityManager, $responseFactory);
    }

    public function index()
    {
        $machine = $this->authManager->guard(config('auth.defaults.guard'))->check()
            ? $this->activeMachineResolver->resolve()
            : null;

        return view(
            'pages.index',
            [
                'machine' => $machine,
            ]
        );
    }

    public function profile(Request $request)
    {
        $error = $request->query->get('error');
        $success = $request->query->get('success');

        return view(
            'pages.profile',
            [
                'error' => $error,
                'success' => $success
            ]
        );
    }

    public function password(Request $request): RedirectResponse
    {
        /** @var UserInterface $user */
        $user = $this->authManager->guard(config('auth.defaults.guard'))->user();

        if ($user->hasPassword()) {
            $currentPassword = $request->get('current-password');

            $password = $request->get('password1');
            $passwordConfirmation = $request->get('password2');

            if (
                $this->hashManager->check($currentPassword, $user->getPassword()) &&
                strlen($password) >= 8 &&
                $password === $passwordConfirmation
            ) {
                $user->setPassword(
                    $this->hashManager->make($password)
                );

                $user->setApiTokenExpiry(null);
                $user->setApiToken(null);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return new RedirectResponse(route('profile') . '?success=newPassword');
            }

            return new RedirectResponse(route('profile') . '?error=invalidNewPassword');
        } else {
            $password = $request->get('password1');
            $passwordConfirmation = $request->get('password2');

            if (strlen($password) >= 8 && $password === $passwordConfirmation) {
                $user->setPassword(
                    $this->hashManager->make($password)
                );

                $user->setApiToken(null);
                $user->setApiTokenExpiry(null);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return new RedirectResponse(route('profile') . '?success=setPassword');
            }

            return new RedirectResponse(route('profile') . '?error=invalidPassword');
        }
    }
}
