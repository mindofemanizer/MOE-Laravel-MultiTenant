<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    |
    | The Eloquent model that represents a tenant in your application.
    | You may replace this with your own model as long as it extends
    | the base Tenant model provided by this package.
    |
    */
    'tenant_model' => 'App\\Models\\Tenant',

    /*
    |--------------------------------------------------------------------------
    | Tenant Column Name
    |--------------------------------------------------------------------------
    |
    | The column name used to store the tenant identifier on tenant-scoped
    | tables (e.g., matters, invoices, clients).
    |
    */
    'tenant_column' => 'tenant_id',

    /*
    |--------------------------------------------------------------------------
    | Tenant Detection Mode
    |--------------------------------------------------------------------------
    |
    | How the system detects which tenant is active for the current request.
    |
    | Supported modes:
    | - 'header'     : Read from a request header (default: X-Tenant)
    | - 'subdomain'  : Extract from the first subdomain (e.g., kantor.aktarahub.id)
    | - 'path'       : Extract from the URL path segment (e.g., /app/{slug}/...)
    | - 'session'    : Read from authenticated user's tenant relationship
    |
    */
    'detection_mode' => env('TENANT_DETECTION_MODE', 'header'),

    /*
    |--------------------------------------------------------------------------
    | Header Detection Mode
    |--------------------------------------------------------------------------
    |
    | When detection_mode is 'header', this header name will be used to
    | resolve the tenant. The value should match the tenant's slug or ID.
    |
    */
    'header_name' => env('TENANT_HEADER', 'X-Tenant'),

    /*
    |--------------------------------------------------------------------------
    | Subdomain Detection Mode
    |--------------------------------------------------------------------------
    |
    | When detection_mode is 'subdomain', the system extracts the tenant
    | identifier from the subdomain and looks it up using the column below.
    |
    */
    'subdomain_column' => 'slug',

    /*
    |--------------------------------------------------------------------------
    | Path Detection Mode
    |--------------------------------------------------------------------------
    |
    | When detection_mode is 'path', this segment index (0-based from
    | the URL path) will be used to resolve the tenant slug.
    |
    */
    'path_segment' => 1,

    /*
    |--------------------------------------------------------------------------
    | Super Admin Flag
    |--------------------------------------------------------------------------
    |
    | The column name on the users table that indicates a super admin.
    | Super admins bypass tenant scoping and can access all tenants.
    |
    */
    'super_admin_flag' => 'is_super_admin',

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Cache resolved tenant instances to reduce database queries.
    |
    */
    'caching' => [
        'enabled' => env('TENANT_CACHE_ENABLED', true),
        'ttl_seconds' => env('TENANT_CACHE_TTL', 3600),
    ],

];
