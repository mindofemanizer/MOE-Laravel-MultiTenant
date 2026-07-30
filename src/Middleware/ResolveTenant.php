<?php

declare(strict_types=1);

namespace MOE\MultiTenant\Middleware;

use Closure;
use Illuminate\Http\Request;
use MOE\MultiTenant\Contracts\TenantContextInterface;
use MOE\MultiTenant\Exceptions\TenantInactiveException;
use MOE\MultiTenant\Exceptions\TenantNotFoundException;

class ResolveTenant
{
    public function __construct(
        protected TenantContextInterface $context
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->resolve($request);

        if (! $tenant) {
            throw new TenantNotFoundException($this->getIdentifier($request));
        }

        if (! $tenant->is_active) {
            throw new TenantInactiveException($tenant->name);
        }

        $this->context->set($tenant);

        return $next($request);
    }

    protected function resolve(Request $request)
    {
        $mode = config('moe-multitenant.detection_mode', 'header');
        $tenantModel = $this->getTenantModel();

        return match ($mode) {
            'subdomain' => $this->resolveFromSubdomain($request, $tenantModel),
            'path' => $this->resolveFromPath($request, $tenantModel),
            'session' => $this->resolveFromSession($request, $tenantModel),
            default => $this->resolveFromHeader($request, $tenantModel),
        };
    }

    protected function resolveFromHeader(Request $request, $model)
    {
        $header = config('moe-multitenant.header_name', 'X-Tenant');
        $value = $request->header($header);

        if (! $value) {
            return null;
        }

        return $model->where('slug', $value)
            ->orWhere($model->getKeyName(), $value)
            ->first();
    }

    protected function resolveFromSubdomain(Request $request, $model)
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        if (count($parts) < 3) {
            return null;
        }

        $slug = $parts[0];
        $column = config('moe-multitenant.subdomain_column', 'slug');

        return $model->where($column, $slug)->first();
    }

    protected function resolveFromPath(Request $request, $model)
    {
        $segment = config('moe-multitenant.path_segment', 1);
        $slug = $request->segment($segment);

        if (! $slug) {
            return null;
        }

        return $model->where('slug', $slug)->first();
    }

    protected function resolveFromSession(Request $request, $model)
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'tenant')) {
            return null;
        }

        return $user->tenant;
    }

    protected function getIdentifier(Request $request): string
    {
        $mode = config('moe-multitenant.detection_mode', 'header');

        return match ($mode) {
            'subdomain' => explode('.', $request->getHost())[0] ?? '',
            'path' => $request->segment(config('moe-multitenant.path_segment', 1)) ?? '',
            'session' => $request->user()?->email ?? '',
            default => $request->header(config('moe-multitenant.header_name', 'X-Tenant'), ''),
        };
    }

    protected function getTenantModel()
    {
        $modelClass = config('moe-multitenant.tenant_model', \MOE\MultiTenant\Models\Tenant::class);

        return new $modelClass;
    }
}
