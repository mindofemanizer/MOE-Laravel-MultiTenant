# MOE Laravel MultiTenant

Multi-tenancy for Laravel — `BelongsToTenant` trait, `TenantScope`, middleware, and tenant context.

## Requirements

- PHP `^8.2`
- Laravel `^11 | ^12 | ^13`

## Installation

```bash
composer require moe/laravel-multi-tenant
php artisan vendor:publish --provider="MOE\\MultiTenant\\MultiTenantServiceProvider" --tag="moe-multitenant-config"
php artisan migrate
```

## Quick Start

### 1. Create your Tenant model

```php
use MOE\MultiTenant\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    // Add your own relationships or methods
}
```

Update `config/moe-multitenant.php`:

```php
'tenant_model' => 'App\\Models\\Tenant',
```

### 2. Apply to models

```php
use MOE\MultiTenant\Traits\BelongsToTenant;

class Matter extends Model
{
    use BelongsToTenant;

    // Queries are automatically scoped to the current tenant
    // tenant_id is auto-filled on creation
}
```

### 3. Register middleware

```php
// bootstrap/app.php
use MOE\MultiTenant\Middleware\ResolveTenant;

->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'tenant' => ResolveTenant::class,
    ]);
})
```

### 4. Protect routes

```php
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', function () {
        $tenant = Tenant::current();
        // ...
    });
});
```

### 5. Set tenant header (API mode)

```http
GET /api/v1/matters HTTP/1.1
X-Tenant: my-office-slug
```

## Usage

### Tenant Context

```php
use MOE\MultiTenant\Facades\Tenant;

// Get current tenant
$tenant = Tenant::current();
$tenantId = Tenant::id();

// Check if tenant context is active
if (Tenant::has()) {
    // ...
}

// Set manually (e.g., from command)
Tenant::set($tenant);

// Clear
Tenant::clear();
```

### Tenant Service

```php
use MOE\MultiTenant\Services\TenantService;

$service = app(TenantService::class);

$tenant = $service->create([
    'name' => 'Kantor Notaris ABC',
    'slug' => 'kantor-abc',
]);

$service->activate($id);
$service->suspend($id);
$service->switchContext($id);
```

### Query Scopes

```php
// Scoped to current tenant (automatic)
$matters = Matter::all();

// Query specific tenant
$matters = Matter::byTenant($tenantId)->get();

// Bypass tenant scope (super admin)
$allMatters = Matter::allTenants()->get();
```

## Configuration

```php
// config/moe-multitenant.php
return [
    'tenant_model' => 'App\\Models\\Tenant',
    'tenant_column' => 'tenant_id',
    'detection_mode' => env('TENANT_DETECTION_MODE', 'header'),

    // Header mode
    'header_name' => env('TENANT_HEADER', 'X-Tenant'),

    // Subdomain mode
    'subdomain_column' => 'slug',

    // Path mode
    'path_segment' => 1,

    // Super admin bypass
    'super_admin_flag' => 'is_super_admin',

    // Caching
    'caching' => [
        'enabled' => true,
        'ttl_seconds' => 3600,
    ],
];
```

## Testing

```bash
composer test
```

## License

MIT © MOE (MindOfEmanizer)
