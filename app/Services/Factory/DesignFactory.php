<?php

declare(strict_types=1);

namespace App\Services\Factory;

use App\Entities\Design;
use App\Entities\DesignInterface;

class DesignFactory implements DesignFactoryInterface
{
    public function createNew(): DesignInterface
    {
        return new Design();
    }
}
