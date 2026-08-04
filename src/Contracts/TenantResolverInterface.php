<?php

declare(strict_types=1);

namespace Moe\MultiTenant\Contracts;

use Illuminate\Http\Request;

interface TenantResolverInterface
{
    public function resolve(Request $request): mixed;
}
