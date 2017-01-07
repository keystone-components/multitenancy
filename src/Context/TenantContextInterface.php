<?php

namespace Keystone\Multitenancy\Context;

use Keystone\Multitenancy\Model\TenantInterface;

interface TenantContextInterface
{
    public function getTenant();

    public function setTenant(TenantInterface $tenant);
}
