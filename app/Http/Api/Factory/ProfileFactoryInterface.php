<?php

declare(strict_types=1);

namespace App\Http\Api\Factory;

use App\Http\Api\Entity\ProfileInterface;

interface ProfileFactoryInterface
{
    public const AUTH_SCH_EMBROIDERY_GROUP_ID = 339;

    public function createFromAuthSchResponse(array $response): ProfileInterface;
}
