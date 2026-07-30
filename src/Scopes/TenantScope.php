<?php

declare(strict_types=1);

namespace MOE\MultiTenant\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use MOE\MultiTenant\Facades\Tenant;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Tenant::has()) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $column = $this->getTenantColumn($model);

        $builder->where($column, Tenant::id());
    }

    public function extend(Builder $builder, Model $model): void
    {
        $builder->macro('byTenant', function (Builder $builder) use ($model) {
            if (! Tenant::has()) {
                return $builder->whereRaw('1 = 0');
            }

            return $builder->where(
                $this->getTenantColumn($model),
                Tenant::id()
            );
        });

        $builder->macro('allTenants', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }

    protected function getTenantColumn(Model $model): string
    {
        return $model->getTenantColumnName ?? config('moe-multitenant.tenant_column', 'tenant_id');
    }
}
