<?php

declare(strict_types=1);

namespace App\Services\Generator;

use App\Entities\UserInterface;
use DateTime;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Str;

class ApiTokenGenerator implements ApiTokenGeneratorInterface
{
    private HashManager $hashManager;

    public function __construct(
        HashManager $hashManager
    ) {
        $this->hashManager = $hashManager;
    }

    public function generate(UserInterface $user): void
    {
        $randomString = Str::random();

        $user->setApiToken($this->hashManager->make($randomString));
        $user->setApiTokenExpiry(
            new DateTime('+1 day')
        );
    }
}
