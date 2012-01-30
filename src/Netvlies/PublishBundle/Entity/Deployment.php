<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\PublishBundle\Entity\Deployment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Deployment
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Netvlies\PublishBundle\Entity\Target $target
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
     * @param object $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Get target
     *
     * @return object 
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