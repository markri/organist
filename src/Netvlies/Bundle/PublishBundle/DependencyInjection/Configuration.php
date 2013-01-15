<?php

namespace Netvlies\Bundle\PublishBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('netvlies_publish');

        $rootNode->children()
            ->scalarNode('repositorypath')->end()
            ->scalarNode('anyterm_user')->end()
            ->scalarNode('anyterm_exec_port')->end()
            ->arrayNode('scm')
            ->useAttributeAsKey('key')
                ->prototype('array')
                ->children()
                    ->scalarNode('key')->end()
                    ->scalarNode('user')->end()
                    ->scalarNode('password')->end()
                ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}