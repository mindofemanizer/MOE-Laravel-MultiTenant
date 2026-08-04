<?php

declare(strict_types=1);

namespace Moe\MultiTenant\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Moe\MultiTenant\Facades\Tenant;
use Moe\MultiTenant\Models\Tenant as TenantModel;
use Moe\MultiTenant\Scopes\TenantScope;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (Model $model) {
            if (! $model->{$model->getTenantColumnName ?? config('moe-multitenant.tenant_column', 'tenant_id')}) {
                $model->forceFill([
                    $model->getTenantColumnName ?? config('moe-multitenant.tenant_column', 'tenant_id') => Tenant::id(),
                ]);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            config('moe-multitenant.tenant_model', TenantModel::class),
            $this->getTenantColumnName ?? config('moe-multitenant.tenant_column', 'tenant_id')
        );
    }

    public function scopeByTenant(Builder $query, string $tenantId): Builder
    {
        return $query->where(
            $this->getTenantColumnName ?? config('moe-multitenant.tenant_column', 'tenant_id'),
            $tenantId
        );
    }

    public function scopeAllTenants(Builder $query): Builder
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }

    public function getTenantId(): ?string
    {
        $column = $this->getTenantColumnName ?? config('moe-multitenant.tenant_column', 'tenant_id');

        return $this->{$column};
    }
}
