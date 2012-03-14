<?php

namespace Netvlies\PublishBundle\DependencyInjection;

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
            ->scalarNode('sudouser')->end()
            ->scalarNode('repositorypath')->end()
            ->arrayNode('scm')
                ->children()
                    ->arrayNode('git')
                        ->useAttributeAsKey('key')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('privatekey')->end()
                                ->scalarNode('owner')->end()
                                ->scalarNode('owner')->end()
                                ->scalarNode('ownerpassword')->end()
                                ->scalarNode('committer')->end()
                                ->scalarNode('committerpassword')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
