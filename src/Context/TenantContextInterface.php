<?php

namespace Keystone\Multitenancy\Context;

use Keystone\Multitenancy\Model\TenantInterface;

interface TenantContextInterface
{
    /**
     * @return TenantInterface|null
     */
    public function getTenant(): ?TenantInterface;

    /**
     * @param TenantInterface $tenant
     */
    public function setTenant(TenantInterface $tenant);
}
