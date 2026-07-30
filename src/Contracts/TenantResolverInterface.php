<?php

declare(strict_types=1);

namespace MOE\MultiTenant\Contracts;

use Illuminate\Http\Request;

interface TenantResolverInterface
{
    public function resolve(Request $request): mixed;
}
