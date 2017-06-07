<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Model;

interface TenantScoped
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
