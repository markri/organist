<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

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
            ->scalarNode('repositorypath')
                ->isRequired()
            ->end()
            ->scalarNode('console_port')->end()
            ->arrayNode('externalstatus')
                ->children()
                    ->booleanNode('bitbucket')
                        ->defaultFalse()
                    ->end()
                    ->booleanNode('github')
                        ->defaultFalse()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
