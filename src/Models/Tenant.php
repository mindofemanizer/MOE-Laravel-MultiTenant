<?php

declare(strict_types=1);

namespace Moe\MultiTenant\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'settings_json',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings_json' => 'array',
    ];

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant) {
            if (! $tenant->getKey()) {
                $tenant->{$tenant->getKeyName()} = (string) str()->uuid();
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
