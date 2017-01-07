<?php

namespace Keystone\Multitenancy\Model;

interface TenantInterface
{
    /**
     * @return mixed
     */
    public function getId();
}
