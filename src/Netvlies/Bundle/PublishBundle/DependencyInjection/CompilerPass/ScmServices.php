<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/28/12
 * Time: 11:32 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


class ScmServices implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $scmservices = $container->findTaggedServiceIds('netvlies_scmservice');
        $services = array();

        foreach ($scmservices as $id => $tags) {
            $services[] = $id;
        }

        $container->setParameter('netvlies_publish.scmtypes', $services);
    }
}
