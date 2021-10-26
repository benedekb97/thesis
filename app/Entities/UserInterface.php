<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\NameableInterface;
use App\Entities\Traits\ResourceInterface;
use App\Entities\Traits\TimestampableInterface;
use DateTimeInterface;
use Illuminate\Contracts\Auth\Authenticatable;

interface UserInterface extends
    ResourceInterface,
    TimestampableInterface,
    NameableInterface,
    Authenticatable
{
    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    public function getPassword(): ?string;

    public function setPassword(?string $password): void;

    public function getApiToken(): ?string;

    public function setApiToken(?string $apiToken): void;

    public function getApiTokenExpiry(): ?DateTimeInterface;

    public function setApiTokenExpiry(?DateTimeInterface $apiTokenExpiry): void;

    public function getAuthSchInternalId(): ?string;

    public function setAuthSchInternalId(?string $authSchInternalId): void;
}
