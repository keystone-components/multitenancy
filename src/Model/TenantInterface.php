<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Model;

interface TenantInterface
{
    /**
     * @return mixed
     */
    public function getTenantId();
}
