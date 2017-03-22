<?php

namespace Keystone\Multitenancy\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Keystone\Multitenancy\Context\TenantContext;
use Keystone\Multitenancy\Model\TenantInterface;
use Keystone\Multitenancy\Model\TenantScoped;
use Mockery;
use RuntimeException;

class TenantScopedEntityListenerTest extends \PHPUnit_Framework_TestCase
{
    private $tenantContext;
    private $listener;

    public function setUp()
    {
        $this->tenantContext = new TenantContext();
        $this->listener = new TenantScopedEntityListener($this->tenantContext);
    }

    public function testIgnoresEntityIfNotTenantScoped()
    {
        $tenant = Mockery::mock(TenantInterface::class);
        $this->tenantContext->setTenant($tenant);

        $entity = Mockery::mock();
        $entity->shouldReceive('setTenant')
            ->never();

        $args = Mockery::mock(LifecycleEventArgs::class, [
            'getEntity' => $entity,
        ]);

        $this->listener->prePersist($args);
    }

    public function testIgnoresEntityIfTenantContextNotSet()
    {
        $entity = Mockery::mock(TenantScoped::class);
        $entity->shouldReceive('setTenant')
            ->never();

        $args = Mockery::mock(LifecycleEventArgs::class, [
            'getEntity' => $entity,
        ]);

        $this->listener->prePersist($args);
    }

    public function testSetsTenantOnTenantScopedEntity()
    {
        $tenant = Mockery::mock(TenantInterface::class);
        $this->tenantContext->setTenant($tenant);

        $entity = Mockery::mock(TenantScoped::class, [
            'getTenant' => null,
        ]);

        $entity->shouldReceive('setTenant')
            ->once()
            ->with($tenant);

        $args = Mockery::mock(LifecycleEventArgs::class, [
            'getEntity' => $entity,
        ]);

        $this->listener->prePersist($args);
    }

    public function testDoesNotUpdateTenant()
    {
        $this->expectException(RuntimeException::class);

        $tenant1 = Mockery::mock(TenantInterface::class);
        $this->tenantContext->setTenant($tenant1);

        $tenant2 = Mockery::mock(TenantInterface::class);
        $entity = Mockery::mock(TenantScoped::class, [
            'getTenant' => $tenant2,
        ]);

        $entity->shouldReceive('setTenant')
            ->never();

        $args = Mockery::mock(LifecycleEventArgs::class, [
            'getEntity' => $entity,
        ]);

        $this->listener->prePersist($args);
    }

    public function testSubscribesToPrePersistEvent()
    {
        $this->assertContains(Events::prePersist, $this->listener->getSubscribedEvents());
    }
}
