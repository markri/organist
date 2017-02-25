<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */

namespace Markri\Bundle\OrganistBundle\Strategy\Commands;


class ActionFactory
{

    private $namespace;

    public function __construct($deploymentStrategy)
    {
        $this->namespace = sprintf('Markri\Bundle\OrganistBundle\Strategy\Commands\%s', ucfirst($deploymentStrategy));
    }

    public function getDeployCommand()
    {
        $class = $this->namespace . '\DeployCommand';
        $this->checkClass($class);
        return new $class;
    }


    public function getInitCommand()
    {
        $class = $this->namespace . '\InitCommand';
        $this->checkClass($class);
        return new $class;
    }


    public function getRollbackCommand()
    {
        $class = $this->namespace . '\RollbackCommand';
        $this->checkClass($class);
        return new $class;
    }

    private function checkClass($class) {
        if (!class_exists($class)) {
            throw new \Exception(sprintf('Class %s doesnt exist. Do you have a correct deployment strategy for your application?', $class));
        }
    }
}
