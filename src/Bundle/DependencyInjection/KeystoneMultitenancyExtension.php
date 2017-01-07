<?php

namespace Keystone\Multitenancy\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KeystoneMultitenancyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $tenantRequestListener = $container->getDefinition('keystone_multitenancy.event_listener.tenant_request');
        $tenantRequestListener->replaceArgument(3, new Reference($config['tenant_repository_id']));
        $tenantRequestListener->replaceArgument(4, $config['tenant_route_parameter']);
        $tenantRequestListener->replaceArgument(5, $config['tenant_filter_column']);
    }
}
