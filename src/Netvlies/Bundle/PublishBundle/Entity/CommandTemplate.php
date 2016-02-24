<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Netvlies\Bundle\PublishBundle\Entity\Strategy;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CommandTemplate
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="template_idx", columns={"strategy_id", "title"})})
 * @ORM\Entity
 */
class CommandTemplate
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var Strategy $strategy
     * @Assert\NotNull(message="Strategy is required")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="Strategy", inversedBy="commandTemplates", fetch="EAGER")
     */
    protected $strategy;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="template", type="text")
     */
    private $template;


    /**
     * @var bool
     * @ORM\Column(name="enabledByDefault", type="boolean")
     */
    private $enabledByDefault;



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
     * Set template
     *
     * @param string $template
     * @return CommandTemplate
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    
        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
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
     * Set strategy
     *
     * @param Strategy $strategy
     * @return CommandTemplate
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
    
        return $this;
    }

    /**
     * Get strategy
     *
     * @return Strategy
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @return boolean
     */
    public function isEnabledByDefault()
    {
        return $this->enabledByDefault;
    }

    /**
     * @param boolean $enabledByDefault
     */
    public function setEnabledByDefault($enabledByDefault)
    {
        $this->enabledByDefault = $enabledByDefault;
    }


}
