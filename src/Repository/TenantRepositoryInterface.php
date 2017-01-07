<?php

namespace Keystone\Multitenancy\Repository;

use Keystone\Multitenancy\Model\TenantInterface;

interface TenantRepositoryInterface
{
    /**
     * @param string $value
     *
     * @return TenantInterface
     */
    public function getByRouteParameter($value);
}
