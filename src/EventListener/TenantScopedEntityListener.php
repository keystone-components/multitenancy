<?php

namespace Keystone\Multitenancy\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use InvalidArgumentException;
use Keystone\Multitenancy\Context\TenantContextInterface;
use Keystone\Multitenancy\Model\TenantScopedInterface;

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
        if (!$entity instanceof TenantScopedInterface || $entity->getTenant() !== null) {
            return;
        }

        if ($this->tenantContext->getTenant() === null) {
            throw new InvalidArgumentException('Tenant context not set');
        }

        $entity->setTenant($this->tenantContext->getTenant());
    }
}
