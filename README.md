# MOE Laravel MultiTenant

Multi-tenancy untuk Laravel — trait `BelongsToTenant`, `TenantScope`, middleware, dan konteks tenant.

## Persyaratan

- PHP `^8.2`
- Laravel `^11 | ^12 | ^13`

## Instalasi

```bash
composer require moe/laravel-multi-tenant
php artisan vendor:publish --provider="MOE\\MultiTenant\\MultiTenantServiceProvider" --tag="moe-multitenant-config"
php artisan migrate
```

## Mulai Cepat

### 1. Buat model Tenant

```php
use MOE\MultiTenant\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    // Tambahkan relasi atau method sendiri
}
```

Update `config/moe-multitenant.php`:

```php
'tenant_model' => 'App\\Models\\Tenant',
```

### 2. Terapkan ke model

```php
use MOE\MultiTenant\Traits\BelongsToTenant;

class Perkara extends Model
{
    use BelongsToTenant;

    // Query otomatis discope ke tenant saat ini
    // tenant_id otomatis terisi saat create
}
```

### 3. Daftarkan middleware

```php
// bootstrap/app.php
use MOE\MultiTenant\Middleware\ResolveTenant;

->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'tenant' => ResolveTenant::class,
    ]);
})
```

### 4. Lindungi route

```php
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', function () {
        $tenant = Tenant::current();
        // ...
    });
});
```

### 5. Header tenant (mode API)

```http
GET /api/v1/perkara HTTP/1.1
X-Tenant: kantor-abc-slug
```

## Penggunaan

### Konteks Tenant

```php
use MOE\MultiTenant\Facades\Tenant;

// Ambil tenant saat ini
$tenant = Tenant::current();
$tenantId = Tenant::id();

// Cek apakah konteks tenant aktif
if (Tenant::has()) {
    // ...
}

// Set manual (misal dari command)
Tenant::set($tenant);

// Hapus
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
// Otomatis discope ke tenant saat ini
$perkara = Perkara::all();

// Query untuk tenant tertentu
$perkara = Perkara::byTenant($tenantId)->get();

// Bypass scope tenant (super admin)
$semuaPerkara = Perkara::allTenants()->get();
```

## Konfigurasi

```php
// config/moe-multitenant.php
return [
    'tenant_model' => 'App\\Models\\Tenant',
    'tenant_column' => 'tenant_id',
    'detection_mode' => env('TENANT_DETECTION_MODE', 'header'),

    // Mode header
    'header_name' => env('TENANT_HEADER', 'X-Tenant'),

    // Mode subdomain
    'subdomain_column' => 'slug',

    // Mode path
    'path_segment' => 1,

    // Super admin bypass
    'super_admin_flag' => 'is_super_admin',

    // Cache
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

## Lisensi

MIT © MOE (MindOfEmanizer)
