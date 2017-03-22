<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Query\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\FilterCollection;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

class TenantScopedFilterTest extends TestCase
{
    private $connection;
    private $entityManager;
    private $filter;

    public function setUp()
    {
        $this->connection = Mockery::mock(Connection::class);
        $this->connection->shouldReceive('quote')
            ->andReturnUsing(function ($value) {
                return "quote($value)";
            });

        $this->entityManager = Mockery::mock(EntityManagerInterface::class, [
            'getConnection' => $this->connection,
            'getFilters' => Mockery::mock(FilterCollection::class)->shouldIgnoreMissing(),
        ]);

        $this->filter = new TenantScopedFilter($this->entityManager);
    }

    public function testDoesNotFilterWhenClassIsNotTenantScoped()
    {
        $reflClass = Mockery::mock(ReflectionClass::class, [
            'implementsInterface' => false,
        ]);

        $targetEntity = Mockery::mock(ClassMetadata::class, [
            'getReflectionClass' => $reflClass,
        ]);

        $this->filter->setColumn('tenant_id');
        $this->filter->setParameter('id', 12);

        $this->assertSame('', $this->filter->addFilterConstraint($targetEntity, 't0'));
    }

    public function testThrowsExceptionWhenColumnNotSet()
    {
        $this->expectException(RuntimeException::class);

        $reflClass = Mockery::mock(ReflectionClass::class, [
            'implementsInterface' => true,
        ]);

        $targetEntity = Mockery::mock(ClassMetadata::class, [
            'getReflectionClass' => $reflClass,
        ]);

        $this->filter->setParameter('id', 12);

        $this->filter->addFilterConstraint($targetEntity, 't0');
    }

    public function testThrowsExceptionWhenParameterNotSet()
    {
        $this->expectException(RuntimeException::class);

        $reflClass = Mockery::mock(ReflectionClass::class, [
            'implementsInterface' => true,
        ]);

        $targetEntity = Mockery::mock(ClassMetadata::class, [
            'getReflectionClass' => $reflClass,
        ]);

        $this->filter->setColumn('tenant_id');

        $this->filter->addFilterConstraint($targetEntity, 't0');
    }

    public function testReturnsFilterSQL()
    {
        $reflClass = Mockery::mock(ReflectionClass::class, [
            'implementsInterface' => true,
        ]);

        $targetEntity = Mockery::mock(ClassMetadata::class, [
            'getReflectionClass' => $reflClass,
        ]);

        $this->filter->setColumn('tenant_id');
        $this->filter->setParameter('id', 12);

        $this->assertSame('t0.tenant_id = quote(12)', $this->filter->addFilterConstraint($targetEntity, 't0'));
    }
}
