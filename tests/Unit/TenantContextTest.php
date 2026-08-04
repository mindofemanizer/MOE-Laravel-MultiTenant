<?php

use Moe\MultiTenant\Contracts\TenantContextInterface;
use Moe\MultiTenant\Facades\Tenant;
use Moe\MultiTenant\Models\Tenant as TenantModel;
use Moe\MultiTenant\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->context = app(TenantContextInterface::class);
    $this->tenant = TenantModel::create([
        'name' => 'Context Office',
        'slug' => 'context-office',
    ]);
});

it('resolves tenant context from singleton', function () {
    expect($this->context)->toBeInstanceOf(TenantContextInterface::class);
});

it('starts with no tenant', function () {
    expect($this->context->has())->toBeFalse();
});

it('can set tenant via context', function () {
    $this->context->set($this->tenant);

    expect($this->context->current()->id)->toBe($this->tenant->id);
});

it('facade delegates to context', function () {
    $this->context->set($this->tenant);

    expect(Tenant::id())->toBe($this->tenant->id);
    expect(Tenant::has())->toBeTrue();
});

it('can clear and reset context', function () {
    $this->context->set($this->tenant);
    expect($this->context->has())->toBeTrue();

    $this->context->clear();
    expect($this->context->has())->toBeFalse();
});

it('context is singleton across app', function () {
    $context1 = app(TenantContextInterface::class);
    $context2 = app(TenantContextInterface::class);

    $context1->set($this->tenant);

    expect($context2->current()->id)->toBe($this->tenant->id);
});
