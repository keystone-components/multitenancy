<?php

namespace Keystone\Multitenancy\Context;

use Keystone\Multitenancy\Model\TenantInterface;

class TenantContext implements TenantContextInterface
{
    /**
     * @var TenantInterface
     */
    private $tenant;

    /**
     * {@inheritdoc}
     */
    public function getTenant(): ?TenantInterface
    {
        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }
}
