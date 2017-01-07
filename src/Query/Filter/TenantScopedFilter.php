<?php

namespace Keystone\Multitenancy\Query\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use InvalidArgumentException;
use Keystone\Multitenancy\Model\TenantScopedInterface;
use RuntimeException;

class TenantScopedFilter extends SQLFilter
{
    private $column;

    public function getColumn()
    {
        return $this->column;
    }

    public function setColumn($column)
    {
        $this->column = $column;
    }

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->reflClass->implementsInterface(TenantScopedInterface::class)) {
            return '';
        }

        if ($this->getColumn() === null) {
            throw new RuntimeException('Tenant query filter column not set');
        }

        if (!$this->hasParameter('id')) {
            throw new InvalidArgumentException('The "id" filter parameter was not set');
        }

        return sprintf(
            '%s.%s = %s',
            $targetTableAlias,
            $this->getColumn(),
            $this->getParameter('id')
        );
    }
}
