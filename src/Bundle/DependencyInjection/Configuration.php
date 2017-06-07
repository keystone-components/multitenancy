<?php

declare(strict_types=1);

namespace Keystone\Multitenancy\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('keystone_multitenancy');

        $rootNode
            ->children()
                ->scalarNode('tenant_entity')
                    ->isRequired()
                    ->info('The entity FQCN')
                ->end()
                ->scalarNode('tenant_repository_id')
                    ->isRequired()
                    ->info('The service ID of the tenant repository implementation')
                ->end()
                ->scalarNode('tenant_route_parameter')
                    ->defaultValue('tenant')
                    ->info('The name of the route parameter used to identify the tenant')
                ->end()
                ->scalarNode('tenant_filter_column')
                    ->defaultValue('tenant_id')
                    ->info('The name of the column used to scope SQL queries to the current tenant')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
