<?php

namespace Keystone\Multitenancy\Model;

interface TenantScopedInterface
{
    /**
     * @return TenantInterface
     */
    public function getTenant();

    /**
     * @param TenantInterface
     */
    public function setTenant(TenantInterface $tenance);
}
