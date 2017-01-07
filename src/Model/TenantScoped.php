<?php

namespace Keystone\Multitenancy\Model;

interface TenantScoped
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
