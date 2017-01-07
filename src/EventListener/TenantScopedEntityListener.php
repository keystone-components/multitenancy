<?php

namespace Keystone\Multitenancy\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Keystone\Multitenancy\Context\TenantContextInterface;
use Keystone\Multitenancy\Model\TenantScopedInterface;
use RuntimeException;

class TenantScopedEntityListener implements EventSubscriber
{
    private $tenantContext;

    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof TenantScopedInterface || $this->tenantContext->getTenant() === null) {
            return;
        }

        if ($entity->getTenant() !== null && $entity->getTenant() !== $this->tenantContext->getTenant()) {
            // The tenant association is set but is different to the current tenant context
            throw new RuntimeException('Cannot persist entity outside of the current tenant scope');
        }

        // Set the current tenant in the association
        $entity->setTenant($this->tenantContext->getTenant());
    }
}
