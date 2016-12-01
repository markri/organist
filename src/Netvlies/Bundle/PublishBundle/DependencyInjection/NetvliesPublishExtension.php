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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NetvliesPublishExtension extends Extension
{

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->containerBuilder = $container;
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Some bundle parameters
        $container->setParameter('netvlies_publish.anyterm_user', $config['anyterm_user']);
        $container->setParameter('netvlies_publish.anyterm_exec_port', $config['anyterm_exec_port']);
        $container->setParameter('netvlies_publish.repositorypath', $config['repositorypath']);
        $container->setParameter('netvlies_publish.bitbucket' , $config['externalstatus']['bitbucket']);
        $container->setParameter('netvlies_publish.github' , $config['externalstatus']['github']);

        $this->processApplicationTypes($config['applicationtypes']);

        $this->processStrategies($config['strategies']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param $applicationTypes
     */
    private function processApplicationTypes($applicationTypes)
    {
        $applicationTypeServices = array();
        $applicationTypeLabels = array();

        foreach ($applicationTypes as $key => $params) {

            $definition = new Definition('Netvlies\Bundle\PublishBundle\ApplicationType\ApplicationType');
            $definition->addMethodCall('setKeyname', array($key));
            $definition->addMethodCall('setLabel', array($params['label']));
            $definition->addMethodCall('setUserdirs', array($params['userdirs']));
            $definition->addMethodCall('setUserfiles', array($params['userfiles']));

            $containerKey = 'netvlies_publish.type.' . $key;

            $this->containerBuilder->setDefinition($containerKey, $definition);

            $applicationTypeServices[] = $key;
            $applicationTypeLabels[$containerKey] = $params['label'];
        }

        $this->containerBuilder->setParameter('netvlies_publish.applicationTypeKeyLabels', $applicationTypeLabels);
    }


    private function processStrategies($strategies)
    {
        $strategyServices = array();
        $strategyLabels = array();

        foreach ($strategies as $key => $params) {
            $definition = new Definition('Netvlies\Bundle\PublishBundle\Strategy\Strategy');
            $definition->addMethodCall('setLabel', array($params['label']));
            $definition->addMethodCall('setKeyname', array($key));
            #$definition->addMethodCall('setRvm', array($params['rvm']));

            $this->containerBuilder->setDefinition($key, $definition);

            $strategyServices[] = $key;
            $strategyLabels[$key] = $params['label'];
        }

        $this->containerBuilder->setParameter('netvlies_publish.strategyKeyLabels', $strategyLabels);
    }
}
