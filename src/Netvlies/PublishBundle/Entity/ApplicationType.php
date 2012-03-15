<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\PublishBundle\Entity\ApplicationType
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ApplicationType
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(name="deployCommand", type="string", length=255)
     */
    private $deployCommand;

    /**
     * @ORM\Column(name="deployOCommand", type="string", length=255)
     */
    private $deployOCommand;

    /**
     * @ORM\Column(name="copyCommand", type="string", length=255)
     */
    private $copyContentCommand;

    /**
     * @ORM\Column(name="rollbackCommand", type="string", length=255)
     */
    private $rollbackCommand;

    /**
     * @ORM\Column(name="setupTAPCommand", type="string", length=255)
     */
    private $setupTAPCommand;



    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setCopyContentCommand($copyContentCommand)
    {
        $this->copyContentCommand = $copyContentCommand;
    }

    public function getCopyContentCommand()
    {
        return $this->copyContentCommand;
    }

    public function setDeployCommand($deployCommand)
    {
        $this->deployCommand = $deployCommand;
    }

    public function getDeployCommand()
    {
        return $this->deployCommand;
    }

    public function setRollbackCommand($rollbackCommand)
    {
        $this->rollbackCommand = $rollbackCommand;
    }

    public function getRollbackCommand()
    {
        return $this->rollbackCommand;
    }

    public function getInitScriptPath()
    {
        return dirname(__DIR__).'/Resources/apptypes/'.$this->getName().'/init.sh';
    }

    public function setDeployOCommand($deployOCommand)
    {
        $this->deployOCommand = $deployOCommand;
    }

    public function getDeployOCommand()
    {
        return $this->deployOCommand;
    }

    public function setSetupTAPCommand($setupTAPCommand)
    {
        $this->setupTAPCommand = $setupTAPCommand;
    }

    public function getSetupTAPCommand()
    {
        return $this->setupTAPCommand;
    }

}