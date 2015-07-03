<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle;

use Netvlies\Bundle\PublishBundle\DependencyInjection\Compiler\VersioningCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NetvliesPublishBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new VersioningCompilerPass());
        parent::build($container);
    }

}
