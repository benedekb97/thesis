<?php

declare(strict_types=1);

namespace App\Factories;

use App\Entities\UserInterface;
use App\Http\Api\Entity\ProfileInterface;

interface UserFactoryInterface
{
    public function createFromAuthSchProfile(ProfileInterface $profile): UserInterface;
}
