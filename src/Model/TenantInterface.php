<?php

namespace Keystone\Multitenancy\Model;

interface TenantInterface
{
    public function getId();

    public function getRouteParameter();
}
