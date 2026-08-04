<?php

declare(strict_types=1);

namespace Moe\MultiTenant;

use Illuminate\Support\ServiceProvider;
use Moe\MultiTenant\Contracts\TenantContextInterface;
use Moe\MultiTenant\Services\TenantContext;

class MultiTenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/moe-multitenant.php', 'moe-multitenant');

        $this->app->singleton(TenantContextInterface::class, function ($app) {
            return new TenantContext();
        });

        $this->app->alias(TenantContextInterface::class, 'moe.tenant');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/moe-multitenant.php' => config_path('moe-multitenant.php'),
        ], 'moe-multitenant-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'moe-multitenant-migrations');
    }
}
