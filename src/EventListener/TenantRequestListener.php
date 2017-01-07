<?php

namespace Keystone\Multitenancy\EventListener;

use Doctrine\ORM\EntityManager;
use Keystone\Multitenancy\Context\TenantContextInterface;
use Keystone\Multitenancy\Exception\TenantNotFoundException;
use Keystone\Multitenancy\Model\TenantInterface;
use Keystone\Multitenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContext;

class TenantRequestListener implements EventSubscriberInterface
{
    private $requestContext;
    private $tenantContext;
    private $tenantRepository;
    private $entityManager;
    private $routeParameter;
    private $filterColumn;

    public function __construct(
        RequestContext $requestContext,
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository,
        EntityManager $entityManager,
        $routeParameter,
        $filterColumn
    ) {
        $this->requestContext = $requestContext;
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
        $this->entityManager = $entityManager;
        $this->routeParameter = $routeParameter;
        $this->filterColumn = $filterColumn;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->attributes->has($this->routeParameter)) {
            // Not a tenant route
            return;
        }

        $identifier = $request->attributes->get($this->routeParameter);
        $tenant = $this->tenantRepository->getByRouteParameter($identifier);
        if (!$tenant) {
            throw new TenantNotFoundException($identifier);
        }

        // Set the tenant context for the request
        $this->tenantContext->setTenant($tenant);
        $this->setRequestContextParameters($tenant);
        $this->enableQueryFilter($tenant);
    }

    private function setRequestContextParameters(TenantInterface $tenant)
    {
        $this->requestContext->setParameter($this->routeParameter, $tenant->getRouteParameter());
    }

    private function enableQueryFilter(TenantInterface $tenant)
    {
        $filter = $this->entityManager->getFilters()->enable('tenant');
        $filter->setParameter('tenantId', $tenant->getId());
        $filter->setColumn($this->filterColumn);
    }

    public static function getSubscribedEvents()
    {
        return [
            // Needs to be called before the RouterListener(32)
            KernelEvents::REQUEST => ['onKernelRequest', 33],
        ];
    }
}
