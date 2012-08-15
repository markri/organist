<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishBundle\Entity\ParameterSetTarget
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ParameterSetTarget extends ParameterSet
{

    /**
     * @var Target $target
     *
     * @ORM\ManyToOne(targetEntity="Target")
     */
    protected $target;


    /**
     * @param \Netvlies\Bundle\PublishBundle\Entity\Target $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return \Netvlies\Bundle\PublishBundle\Entity\Target
     */
    public function getTarget()
    {
        return $this->target;
    }
}