<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishBundle\Entity\Command
 * @todo we should also have an applicationCommand entity, where all the commands for specific apptype are copied to, so we can edit and add application specific commands afterwards
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Netvlies\Bundle\PublishBundle\Entity\CommandRepository")
 */
class Command
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected  $id;

    /**
     * @var ApplicationType
     *
     * @ORM\ManyToOne(targetEntity="ApplicationType")
     */
    protected $applicationType;

    /**
     * @var string $displayName
     *
     * @ORM\Column(name="displayName", type="string", length=255)
     */
    protected $displayName;

    /**
     * @var string $command
     *
     * @ORM\Column(name="command", type="string", length=255)
     */
    protected $command;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     * @return Command
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Get commandLabel
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set command
     *
     * @param string $command
     * @return Command
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Get command
     *
     * @return string 
     */
    public function getCommand()
    {
        return $this->command;
    }

    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
    }

    public function getApplicationType()
    {
        return $this->applicationType;
    }
}