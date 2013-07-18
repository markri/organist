<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishBundle\Entity\ParameterSetCommand
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ParameterSetCommand extends ParameterSet
{

    /**
     * @var Command $command
     *
     * @ORM\ManyToOne(targetEntity="Command")
     */
    protected $command;


    /**
     * @param \Netvlies\Bundle\PublishBundle\Entity\Command $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return \Netvlies\Bundle\PublishBundle\Entity\Command
     */
    public function getCommand()
    {
        return $this->command;
    }
}