<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Keystone\Multitenancy\Context\TenantContextInterface;
use Keystone\Multitenancy\Exception\TenantNotFoundException;
use Keystone\Multitenancy\Model\TenantInterface;
use Keystone\Multitenancy\Query\Filter\TenantScopedFilter;
use Keystone\Multitenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContext;

class TenantRequestListener implements EventSubscriberInterface
{
    /**
     * @var RequestContext
     */
    private $requestContext;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * @var string
     */
    private $routeParameter;

    /**
     * @var string
     */
    private $filterColumn;

    /**
     * @param RequestContext $requestContext
     * @param TenantContextInterface $tenantContext
     * @param EntityManagerInterface $entityManager
     * @param TenantRepositoryInterface $tenantRepository
     * @param string $routeParameter
     * @param string $filterColumn
     */
    public function __construct(
        RequestContext $requestContext,
        TenantContextInterface $tenantContext,
        EntityManagerInterface $entityManager,
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

    /**
     * @param GetResponseEvent $event
     *
     * @throws TenantNotFoundException
     */
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
        $tenant = $this->tenantRepository->getByTenantRouteParameter($identifier);
        if (!$tenant) {
            throw new TenantNotFoundException($identifier);
        }

        // Set the tenant context for the request
        $this->tenantContext->setTenant($tenant);
        $this->requestContext->setParameter($this->routeParameter, $identifier);
        $this->enableQueryFilter($tenant);
    }

    /**
     * @param TenantInterface $tenant
     */
    private function enableQueryFilter(TenantInterface $tenant)
    {
        $filters = $this->entityManager->getFilters();
        if (!$filters->has('tenant')) {
            // Do not try to enable if it is not configured
            return;
        }

        /** @var TenantScopedFilter $filter */
        $filter = $filters->enable('tenant');
        $filter->setParameter('id', $tenant->getTenantId());
        $filter->setColumn($this->filterColumn);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            // Needs to be called after the RouterListener(32)
            KernelEvents::REQUEST => ['onKernelRequest', 31],
        ];
    }
}
