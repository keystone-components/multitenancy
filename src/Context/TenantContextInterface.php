<?php

namespace Keystone\Multitenancy\Context;

use Keystone\Multitenancy\Model\TenantInterface;

interface TenantContextInterface
{
    /**
     * @param getTenant
     */
    public function getTenant();

    /**
     * @param TenantInterface $tenant
     */
    public function setTenant(TenantInterface $tenant);
}
