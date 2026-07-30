<?php

declare(strict_types=1);

namespace MOE\MultiTenant\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MOE\MultiTenant\Exceptions\TenantNotFoundException;
use MOE\MultiTenant\Models\Tenant;

class TenantService
{
    public function __construct(
        protected TenantContext $context
    ) {}

    public function create(array $data): Tenant
    {
        $data['slug'] ??= Str::slug($data['name'] ?? '');
        $data['is_active'] ??= true;

        return Tenant::create($data);
    }

    public function findById(string $id): ?Tenant
    {
        return Tenant::find($id);
    }

    public function findBySlug(string $slug): ?Tenant
    {
        return Tenant::bySlug($slug)->first();
    }

    public function activate(string $id): Tenant
    {
        $tenant = $this->findOrFail($id);
        $tenant->update(['is_active' => true]);

        return $tenant;
    }

    public function suspend(string $id): Tenant
    {
        $tenant = $this->findOrFail($id);
        $tenant->update(['is_active' => false]);

        return $tenant;
    }

    public function switchContext(string $id): void
    {
        $tenant = $this->findOrFail($id);
        $this->context->set($tenant);
    }

    public function delete(string $id): bool
    {
        $tenant = $this->findOrFail($id);

        return $tenant->delete();
    }

    public function all(): iterable
    {
        return Tenant::all();
    }

    public function active(): iterable
    {
        return Tenant::active()->get();
    }

    protected function findOrFail(string $id): Tenant
    {
        $tenant = Tenant::find($id);

        if (! $tenant) {
            throw new TenantNotFoundException($id);
        }

        return $tenant;
    }

    public function current(): ?Tenant
    {
        return $this->context->current();
    }
}
