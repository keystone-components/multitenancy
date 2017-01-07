<?php

namespace Keystone\Multitenancy\Exception;

use Exception;

class TenantNotFoundException extends Exception
{
    public function __construct($identifier, $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf('Tenant "%s" not found', $identifier), $code, $previous);
    }
}
