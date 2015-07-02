<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */

namespace Netvlies\Bundle\PublishBundle\Action;


class ActionFactory
{

    private $namespace;

    public function __construct($deploymentStrategy)
    {
        $this->namespace = sprintf('Netvlies\Bundle\PublishBundle\Action\%s', ucfirst($deploymentStrategy));
    }

    public function getDeployCommand()
    {
        $class = $this->namespace . '\DeployCommand';
        return new $class;
    }


    public function getInitCommand()
    {
        $class = $this->namespace . '\InitCommand';
        return new $class;
    }


    public function getRollbackCommand()
    {
        $class = $this->namespace . '\RollbackCommand';
        return new $class;
    }
}
