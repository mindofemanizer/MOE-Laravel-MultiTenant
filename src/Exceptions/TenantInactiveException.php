<?php

declare(strict_types=1);

namespace Moe\MultiTenant\Exceptions;

use Exception;

class TenantInactiveException extends Exception
{
    public function __construct(string $tenantName = '')
    {
        $message = 'Tenant is inactive.';
        if ($tenantName) {
            $message = "Tenant [{$tenantName}] is inactive. Access denied.";
        }

        parent::__construct($message, 403);
    }
}
