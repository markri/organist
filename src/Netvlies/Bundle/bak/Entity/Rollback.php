<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishBundle\Entity\Rollback
 */
class Rollback
{
    /**
     * @var \Netvlies\Bundle\PublishBundle\Entity\Target $target
     *
     */
    private $target;


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
}