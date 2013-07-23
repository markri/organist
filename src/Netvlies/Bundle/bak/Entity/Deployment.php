<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishBundle\Entity\Deployment
 */
class Deployment
{
    /**
     * @var \Netvlies\Bundle\PublishBundle\Entity\Target $target
     *
     */
    private $target;

    /**
     * @var string $reference
     */
    private $reference;


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
     * Set target
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Target $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Get target
     *
     * @return \Netvlies\Bundle\PublishBundle\Entity\Target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set reference
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }
}