<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ScheduledDeployment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ScheduledDeployment
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
     * @var Application
     *
     * @ORM\ManyToOne(targetEntity="Application", inversedBy="scheduledDeployments")
     */
    private $application;

    /**
     * @var Target
     *
     * @ORM\ManyToOne(targetEntity="Target")
     * @Assert\NotBlank(message="Target is required")
     */
    private $target;

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string", length=255)
     * @Assert\NotBlank(message="Branch is required")
     */
    private $branch;

    /**
     * @var string
     *
     * @ORM\Column(name="time", type="string", length=255)
     * @Assert\NotBlank(message="Time is required")
     */
    private $time;


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
     * Set application
     *
     * @param Application $application
     * @return ScheduledDeployment
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    
        return $this;
    }

    /**
     * Get application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set target
     *
     * @param Target $target
     * @return ScheduledDeployment
     */
    public function setTarget(Target $target)
    {
        $this->target = $target;
    
        return $this;
    }

    /**
     * Get target
     *
     * @return Target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set branch
     *
     * @param string $branch
     * @return ScheduledDeployment
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    
        return $this;
    }

    /**
     * Get branch
     *
     * @return string 
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set time
     *
     * @param string $time
     * @return ScheduledDeployment
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }
}
