<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\Bundle\PublishBundle\Entity\UserFile
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserFile
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
     * @ORM\ManyToOne(targetEntity="Application")
     */
    private $application;

    /**
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string $type
     * @Assert\Choice(choices = {"F", "D"}, message="Choose a valid type (D)irectory or (F)ile.")
     * @ORM\Column(name="type", type="string", length=1)
     */
    private $type;


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
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function __toString(){
        return 'a';
    }
}