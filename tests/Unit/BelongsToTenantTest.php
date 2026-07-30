<?php

declare(strict_types=1);

use MOE\MultiTenant\Facades\Tenant;
use MOE\MultiTenant\Models\Tenant as TenantModel;
use MOE\MultiTenant\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->tenant = TenantModel::create([
        'name' => 'Test Office',
        'slug' => 'test-office',
    ]);

    Tenant::set($this->tenant);
});

it('can set and get current tenant', function () {
    expect(Tenant::current())->toBeInstanceOf(TenantModel::class);
    expect(Tenant::id())->toBe($this->tenant->id);
    expect(Tenant::has())->toBeTrue();
});

it('can clear tenant context', function () {
    Tenant::clear();

    expect(Tenant::has())->toBeFalse();
    expect(Tenant::current())->toBeNull();
    expect(Tenant::id())->toBeNull();
});

it('creates tenant with uuid primary key', function () {
    expect($this->tenant->getKey())->toBeString();
    expect(strlen($this->tenant->getKey()))->toBe(36);
});

it('creates tenant with active default', function () {
    expect($this->tenant->is_active)->toBeTrue();
});

it('can find tenant by slug', function () {
    $found = TenantModel::bySlug('test-office')->first();

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($this->tenant->id);
});

it('scopes active tenants', function () {
    $inactive = TenantModel::create([
        'name' => 'Inactive Office',
        'slug' => 'inactive-office',
        'is_active' => false,
    ]);

    $active = TenantModel::active()->get();

    expect($active)->toHaveCount(1);
    expect($active->first()->id)->toBe($this->tenant->id);
});
