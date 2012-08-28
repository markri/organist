<?php

namespace Netvlies\Bundle\PublishBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Netvlies\Bundle\PublishBundle\DependencyInjection\CompilerPass\ScmServices;

class NetvliesPublishBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ScmServices());
    }

}
