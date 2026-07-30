<?php

declare(strict_types=1);

namespace MOE\MultiTenant\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Model|null current()
 * @method static void set(\Illuminate\Database\Eloquent\Model $tenant)
 * @method static bool has()
 * @method static string|null id()
 * @method static void clear()
 *
 * @see \MOE\MultiTenant\Contracts\TenantContextInterface
 */
class Tenant extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'moe.tenant';
    }
}
