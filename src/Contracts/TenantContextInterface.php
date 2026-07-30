<?php

declare(strict_types=1);

namespace MOE\MultiTenant\Contracts;

use Illuminate\Database\Eloquent\Model;

interface TenantContextInterface
{
    public function current(): ?Model;

    public function set(Model $tenant): void;

    public function has(): bool;

    public function id(): ?string;

    public function clear(): void;
}
