<?php

declare(strict_types=1);

namespace MOE\MultiTenant\Services;

use Illuminate\Database\Eloquent\Model;
use MOE\MultiTenant\Contracts\TenantContextInterface;

class TenantContext implements TenantContextInterface
{
    protected ?Model $tenant = null;

    public function current(): ?Model
    {
        return $this->tenant;
    }

    public function set(Model $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }

    public function id(): ?string
    {
        return $this->tenant?->getKey();
    }

    public function clear(): void
    {
        $this->tenant = null;
    }
}
