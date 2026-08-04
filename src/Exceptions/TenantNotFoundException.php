<?php

declare(strict_types=1);

namespace Moe\MultiTenant\Exceptions;

use Exception;

class TenantNotFoundException extends Exception
{
    public function __construct(string $identifier = '')
    {
        $message = 'Tenant not found.';
        if ($identifier) {
            $message = "Tenant [{$identifier}] not found.";
        }

        parent::__construct($message, 404);
    }
}
