<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\NameableInterface;
use App\Entities\Traits\ResourceInterface;
use App\Entities\Traits\TimestampableInterface;
use Illuminate\Contracts\Auth\Authenticatable;

interface UserInterface extends
    ResourceInterface,
    TimestampableInterface,
    NameableInterface,
    Authenticatable
{

}
