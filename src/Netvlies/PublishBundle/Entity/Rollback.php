<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\PublishBundle\Entity\Rollback
 */
class Rollback
{
    /**
     * @var \Netvlies\PublishBundle\Entity\Target $target
     *
     */
    private $target;


    /**
     * Set target
     *
     * @param \Netvlies\PublishBundle\Entity\Target $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Get target
     *
     * @return \Netvlies\PublishBundle\Entity\Target
     */
    public function getTarget()
    {
        return $this->target;
    }
}