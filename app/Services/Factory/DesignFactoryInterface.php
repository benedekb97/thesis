<?php

declare(strict_types=1);

namespace App\Services\Factory;

use App\Entities\DesignInterface;

interface DesignFactoryInterface
{
    public function createNew(): DesignInterface;
}
