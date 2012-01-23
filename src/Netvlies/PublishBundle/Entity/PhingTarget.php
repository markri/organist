<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\PublishBundle\Entity\PhingTarget
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Netvlies\PublishBundle\Entity\PhingTargetRepository")
 */
class PhingTarget
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     *
     * @ORM\ManyToOne(targetEntity="Application")
     */
    private $application;


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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


    public function setApplication($application)
    {
        $this->application = $application;
    }



    public function getApplication()
    {
        return $this->application;
    }
}