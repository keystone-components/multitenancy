<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Controller\ArgumentResolver;

use Keystone\Multitenancy\Context\TenantContextInterface;
use Keystone\Multitenancy\Model\TenantInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class TenantValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var TenantContextInterface
     */
    private $context;

    /**
     * @var string
     */
    private $type;

    /**
     * @param TenantContextInterface $context
     */
    public function __construct(TenantContextInterface $context, string $type = TenantInterface::class)
    {
        $this->context = $context;
        $this->type = $type;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $argument->getType() === $this->type && $this->context->getTenant() !== null;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return TenantInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->context->getTenant();
    }
}
