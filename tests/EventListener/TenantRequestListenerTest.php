<?php

namespace Keystone\Multitenancy\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\FilterCollection;
use Keystone\Multitenancy\Context\TenantContext;
use Keystone\Multitenancy\Model\TenantInterface;
use Keystone\Multitenancy\Query\Filter\TenantScopedFilter;
use Keystone\Multitenancy\Repository\TenantRepositoryInterface;
use Mockery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RequestContext;

class TenantRequestListenerTest extends \PHPUnit_Framework_TestCase
{
    private $requestContext;
    private $tenantContext;
    private $tenantRepository;
    private $entityManager;
    private $listener;

    public function setUp()
    {
        $this->requestContext = new RequestContext();
        $this->tenantContext = new TenantContext();
        $this->tenantRepository = Mockery::mock(TenantRepositoryInterface::class);

        $this->entityManager = Mockery::mock(EntityManager::class);

        $filterCollection = Mockery::mock(FilterCollection::class)
            ->shouldIgnoreMissing();

        $filterCollection->shouldReceive('enable')
            ->with('tenant')
            ->andReturn(new TenantScopedFilter($this->entityManager));

        $this->entityManager->shouldReceive('getFilters')
            ->andReturn($filterCollection);

        $this->listener = new TenantRequestListener(
            $this->requestContext,
            $this->tenantContext,
            $this->tenantRepository,
            $this->entityManager,
            'tenant',
            'tenant_id'
        );
    }

    public function testIgnoreSubRequests()
    {
        $request = new Request();
        $request->attributes->set('tenant', 'test');

        $event = Mockery::mock(GetResponseEvent::class, [
            'isMasterRequest' => false,
            'getRequest' => $request,
        ]);

        $this->tenantRepository->shouldReceive('getByRouteParameter')
            ->never();

        $this->listener->onKernelRequest($event);

        $this->assertNull($this->tenantContext->getTenant());
    }

    public function testIgnoreRequestsWithoutTheTenantAttribute()
    {
        $request = new Request();

        $event = Mockery::mock(GetResponseEvent::class, [
            'isMasterRequest' => true,
            'getRequest' => $request,
        ]);

        $this->tenantRepository->shouldReceive('getByRouteParameter')
            ->never();

        $this->listener->onKernelRequest($event);

        $this->assertNull($this->tenantContext->getTenant());
    }

    public function testSetsTenantRequestContext()
    {
        $request = new Request();
        $request->attributes->set('tenant', 'test');

        $event = Mockery::mock(GetResponseEvent::class, [
            'isMasterRequest' => true,
            'getRequest' => $request,
        ]);

        $tenant = Mockery::mock(TenantInterface::class, [
            'getId' => 1,
            'getRouteParameter' => 'test',
        ]);

        $this->tenantRepository->shouldReceive('getByRouteParameter')
            ->once()
            ->with('test')
            ->andReturn($tenant);

        $this->listener->onKernelRequest($event);

        $this->assertSame('test', $this->requestContext->getParameter('tenant'));
    }

    public function testSetsTenantContext()
    {
        $request = new Request();
        $request->attributes->set('tenant', 'test');

        $event = Mockery::mock(GetResponseEvent::class, [
            'isMasterRequest' => true,
            'getRequest' => $request,
        ]);

        $tenant = Mockery::mock(TenantInterface::class, [
            'getId' => 1,
            'getRouteParameter' => 'test',
        ]);

        $this->tenantRepository->shouldReceive('getByRouteParameter')
            ->once()
            ->with('test')
            ->andReturn($tenant);

        $this->listener->onKernelRequest($event);

        $this->assertSame($tenant, $this->tenantContext->getTenant());
    }
}
