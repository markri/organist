<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishBundle\Entity\ParameterSetEnvironment
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ParameterSetEnvironment extends ParameterSet
{

    /**
     * @var Environment $environment
     *
     * @ORM\ManyToOne(targetEntity="Environment")
     */
    protected $environment;


    /**
     * @param \Netvlies\Bundle\PublishBundle\Entity\Environment $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return \Netvlies\Bundle\PublishBundle\Entity\Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}