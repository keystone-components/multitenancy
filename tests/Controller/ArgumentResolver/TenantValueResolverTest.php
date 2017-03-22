<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Controller\ArgumentResolver;

use Keystone\Multitenancy\Context\TenantContext;
use Keystone\Multitenancy\Model\TenantInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class TenantValueResolverTest extends TestCase
{
    private $context;
    private $resolver;

    public function setUp()
    {
        $this->context = new TenantContext();
        $this->resolver = new TenantValueResolver($this->context);
    }

    public function testSupportsTenantArgumentWhenContextSet()
    {
        $this->context->setTenant(Mockery::mock(TenantInterface::class));

        $argument = new ArgumentMetadata('test', TenantInterface::class, false, false, null);

        $this->assertTrue($this->resolver->supports(new Request(), $argument));
    }

    public function testDoesNotSupportTenantArgumentWhenContextNotSet()
    {
        $argument = new ArgumentMetadata('test', TenantInterface::class, false, false, null);

        $this->assertFalse($this->resolver->supports(new Request(), $argument));
    }

    public function testDoesNotSupportNonTenantArgument()
    {
        $argument = new ArgumentMetadata('test', 'string', false, false, null);

        $this->assertFalse($this->resolver->supports(new Request(), $argument));
    }

    public function testResolveReturnsTenantFromContext()
    {
        $tenant = Mockery::mock(TenantInterface::class);
        $this->context->setTenant($tenant);

        $argument = new ArgumentMetadata('test', TenantInterface::class, false, false, null);
        $generator = $this->resolver->resolve(new Request(), $argument);

        $this->assertSame($tenant, iterator_to_array($generator)[0]);
    }
}
