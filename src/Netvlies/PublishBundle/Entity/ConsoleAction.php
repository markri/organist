<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 8-2-12
 * Time: 12:54
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\PublishBundle\Entity;

class ConsoleAction
{

    /**
     * @var \Netvlies\PublishBundle\Entity\Application $application
     */
    private $application;

    /**
     * @var \Netvlies\PublishBundle\Entity\ApplicationType $applicationType
     */
    private $applicationType;

    /**
     * @var \Netvlies\PublishBundle\Entity\Environment $environment
     */
    private $environment;

    /**
     * @var \Netvlies\PublishBundle\Entity\Target $target
     */
    private $target;

    /**
     * @var \Netvlies\PublishBundle\Entity\Target $sourceTarget
     */
    private $sourceTarget;

    /**
     * @var \Netvlies\PublishBundle\Entity\Target $destTarget
     */
    private $destTarget;

    /**
     * @var string $revision
     */
    private $revision;

    /**
     * @var object $container
     */
    private $container;

    /**
     * @var array|string $command
     */
    private $command;


    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        if(!is_null($this->target)){
            return $this->target->getApplication();
        }
        return $this->application;
    }

    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
    }

    public function getApplicationType()
    {
        if(!is_null($this->getApplication())){
            return $this->getApplication()->getType();
        }
        return $this->applicationType;
    }

    public function setDestTarget($destTarget)
    {
        $this->destTarget = $destTarget;
    }

    public function getDestTarget()
    {
        return $this->destTarget;
    }

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    public function getEnvironment()
    {
        if(!is_null($this->target)){
            return $this->target->getEnvironment();
        }
        return $this->environment;
    }

    public function setRevision($revision)
    {
        $this->revision = $revision;
    }

    public function getRevision()
    {
        return $this->revision;
    }

    public function setSourceTarget($sourceTarget)
    {
        $this->sourceTarget = $sourceTarget;
    }

    public function getSourceTarget()
    {
        return $this->sourceTarget;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setCommand($command)
    {
        $this->command = $command;
    }

    public function getCommand()
    {
        return $this->command;
    }
}
