<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Repository;

use Keystone\Multitenancy\Model\TenantInterface;

interface TenantRepositoryInterface
{
    /**
     * @param string $value
     *
     * @return TenantInterface|null
     */
    public function getByTenantRouteParameter(string $value): ?TenantInterface;
}
