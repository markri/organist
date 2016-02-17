<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */

namespace Netvlies\Bundle\PublishBundle\Strategy\Commands;


abstract class BaseUpdateCommand implements CommandTargetInterface
{

    /**
     * @var Application $application
     */
    protected $application;

    /**
     * @var Target $target
     */
    protected $target;

    /**
     * @return string
     */
    protected function getUpdateVersionScript()
    {
        $appRoot = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));

        return sprintf('
            && echo "Finished updating process. Retrieving current version ..."
            && cd %s
            && app/console organist:updateversion --id=%s
            && echo ""',
            $appRoot, $this->target->getId());
    }

}
