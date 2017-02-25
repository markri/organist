<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */

namespace Markri\Bundle\OrganistBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;


class VersioningCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('organist.versioning');
        $versioningServices = array();
        $versioningLabels = array();

        foreach ($taggedServices as $id => $tags) {
            $versioningServices[$id] = $id;
            $label = ucfirst($id);

            if (!empty($tags)) {
                $tag = array_pop($tags);
                $label = $tag['alias'];
            }

            $versioningLabels[$id] = $label;
        }

        $container->setParameter('organist.versioningKeyLabels', $versioningLabels);
    }
}
