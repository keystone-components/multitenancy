<?php

namespace Keystone\Multitenancy\Repository;

interface TenantRepositoryInterface
{
    public function getByRouteParameter($value);
}
