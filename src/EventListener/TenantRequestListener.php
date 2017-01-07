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
    private $entityManager;
    private $tenantRepository;
    private $routeParameter;
    private $filterColumn;

    public function __construct(
        RequestContext $requestContext,
        TenantContextInterface $tenantContext,
        EntityManager $entityManager,
        TenantRepositoryInterface $tenantRepository,
        $routeParameter,
        $filterColumn
    ) {
        $this->requestContext = $requestContext;
        $this->tenantContext = $tenantContext;
        $this->entityManager = $entityManager;
        $this->tenantRepository = $tenantRepository;
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
        $this->requestContext->setParameter($this->routeParameter, $identifier);
        $this->enableQueryFilter($tenant);
    }

    private function enableQueryFilter(TenantInterface $tenant)
    {
        $filters = $this->entityManager->getFilters();
        if (!$filters->has('tenant')) {
            // Do not try to enable if it is not configured
            return;
        }

        $filter = $filters->enable('tenant');
        $filter->setParameter('tenantId', $tenant->getId());
        $filter->setColumn($this->filterColumn);
    }

    public static function getSubscribedEvents()
    {
        return [
            // Needs to be called after the RouterListener(32)
            KernelEvents::REQUEST => ['onKernelRequest', 31],
        ];
    }
}
