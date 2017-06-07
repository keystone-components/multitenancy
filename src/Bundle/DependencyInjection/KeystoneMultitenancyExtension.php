<?php

declare(strict_types=1);

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

        $requestListener = $container->getDefinition('keystone_multitenancy.event_listener.tenant_request');
        $requestListener->replaceArgument(3, new Reference($config['tenant_repository_id']));
        $requestListener->replaceArgument(4, $config['tenant_route_parameter']);
        $requestListener->replaceArgument(5, $config['tenant_filter_column']);

        $argumentResolver = $container->getDefinition('keystone_multitenancy.controller.argument_resolver.tenant_value');
        $argumentResolver->replaceArgument(1, $config['tenant_entity']);
    }
}
