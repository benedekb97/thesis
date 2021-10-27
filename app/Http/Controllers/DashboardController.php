<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entities\UserInterface;
use Doctrine\ORM\EntityManager;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Illuminate\Auth\AuthManager;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    private AuthManager $authManager;

    public function __construct(
        EntityManager $entityManager,
        ResponseFactory $responseFactory,
        AuthManager $authManager
    )
    {
        $this->authManager = $authManager;

        parent::__construct($entityManager, $responseFactory);
    }

    public function index(Request $request)
    {
//        /** @var UserInterface $user */
//        $user = $this->authManager->guard(config('auth.defaults.guard'))->user();


        return view(
            'pages.index',
            [
//                'user' => $user,
            ]
        );
    }

    public function profile()
    {
        return view('pages.profile');
    }
}
