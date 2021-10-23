<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\NameableTrait;
use App\Entities\Traits\ResourceTrait;
use App\Entities\Traits\TimestampableTrait;
use DateTimeInterface;

class User implements UserInterface
{
    use ResourceTrait;
    use TimestampableTrait;
    use NameableTrait;

    private ?string $email = null;

    private ?string $password = null;

    private ?string $apiToken = null;

    private ?DateTimeInterface $apiTokenExpiry = null;

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): ?int
    {
        return $this->id;
    }

    public function getAuthPassword(): ?string
    {
        return $this->password;
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        return;
    }

    public function getRememberTokenName(): ?string
    {
        return null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): void
    {
        $this->apiToken = $apiToken;
    }

    public function getApiTokenExpiry(): ?DateTimeInterface
    {
        return $this->apiTokenExpiry;
    }

    public function setApiTokenExpiry(?DateTimeInterface $apiTokenExpiry): void
    {
        $this->apiTokenExpiry = $apiTokenExpiry;
    }
}
