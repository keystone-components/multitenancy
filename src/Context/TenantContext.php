<?php

namespace Keystone\Multitenancy\Context;

use Keystone\Multitenancy\Model\TenantInterface;

class TenantContext implements TenantContextInterface
{
    private $tenant;

    public function getTenant()
    {
        return $this->tenant;
    }

    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }
}
