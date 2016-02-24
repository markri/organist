<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Strategy
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Strategy
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    protected $title;


    /**
     * @var object $commandTemplates
     * @ORM\OneToMany(targetEntity="CommandTemplate", mappedBy="strategy")
     */
    private $commandTemplates;

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
     * Set title
     *
     * @param string $title
     * @return CommandTemplate
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return object
     */
    public function getCommandTemplates()
    {
        return $this->commandTemplates;
    }

    /**
     * @param object $commandTemplates
     */
    public function setCommandTemplates($commandTemplates)
    {
        $this->commandTemplates = $commandTemplates;
    }

    public function __toString()
    {
        return $this->title;
    }

}
