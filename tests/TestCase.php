<?php

declare(strict_types=1);

namespace MOE\MultiTenant\Tests;

use MOE\MultiTenant\MultiTenantServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MultiTenantServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Tenant' => \MOE\MultiTenant\Facades\Tenant::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('moe-multitenant.tenant_model', \MOE\MultiTenant\Models\Tenant::class);
        $app['config']->set('moe-multitenant.detection_mode', 'header');
        $app['config']->set('moe-multitenant.header_name', 'X-Tenant');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
