<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Bundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KeystoneMultitenancyExtensionTest extends TestCase
{
    public function testRequiresTenantRepositoryId()
    {
        $this->expectException(InvalidConfigurationException::class);

        $container = $this->createContainer();
        $this->loadConfig($container, []);
    }

    public function testTenantRequestListenerService()
    {
        $container = $this->createContainer();
        $this->loadConfig($container, [
            'tenant_repository_id' => 'tenant_repository',
            'tenant_route_parameter' => 'tenantId',
            'tenant_filter_column' => 'tenant_id',
        ]);

        $definition = $container->getDefinition('keystone_multitenancy.event_listener.tenant_request');

        $this->assertSame('tenant_repository', (string) $definition->getArgument(3));
        $this->assertSame('tenantId', $definition->getArgument(4));
        $this->assertSame('tenant_id', $definition->getArgument(5));
    }

    public function testConfigurationDefaultTenantRouteParameter()
    {
        $container = $this->createContainer();
        $this->loadConfig($container, [
            'tenant_repository_id' => 'tenant_repository',
        ]);

        $definition = $container->getDefinition('keystone_multitenancy.event_listener.tenant_request');
        $this->assertSame('tenant', $definition->getArgument(4));
    }

    public function testConfigurationDefaultTenantFilterColumn()
    {
        $container = $this->createContainer();
        $this->loadConfig($container, [
            'tenant_repository_id' => 'tenant_repository',
        ]);

        $definition = $container->getDefinition('keystone_multitenancy.event_listener.tenant_request');
        $this->assertSame('tenant_id', $definition->getArgument(5));
    }

    private function loadConfig(ContainerBuilder $container, array $config = [])
    {
        $extension = new KeystoneMultitenancyExtension();
        $extension->load([$config], $container);
    }

    private function createContainer($debug = true)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', $debug);

        return $container;
    }
}
