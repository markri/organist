<?php

namespace Netvlies\PublishBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Dumper\GraphvizDumper;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NetvliesPublishExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('netvlies_publish.sudouser', $config['sudouser']);
        $container->setParameter('netvlies_publish.repositorypath', $config['repositorypath']);


        foreach($config['scm'] as $scmType => $scm){
            foreach($scm as $scmService => $serviceParams){
                foreach($serviceParams as $key=>$value){
                    $container->setParameter('netvlies_publish.'.$scmType.'.'.$scmService.'.'.$key, $value);
                }
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
