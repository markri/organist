<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\PublishBundle\Entity\Deployment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Netvlies\PublishBundle\Entity\DeploymentRepository")
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
     * @var Environment $environment
     * @Assert\NotBlank(message="environment is required/key could not be found")
     * @ORM\ManyToOne(targetEntity="Environment")
     */
    private $environment;
	
    /**
     * @var string $label
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */	
	private $label;

    /**
     * @var Application $application
     * @Assert\NotBlank(message="application is required/key could not be found")
     * @ORM\ManyToOne(targetEntity="Application")
     */
    private $application;

    /**
     * @var string $username
     * @Assert\NotBlank(message="username is required")
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string $currentRevision
     *
     * @ORM\Column(name="mysqldb", type="string", length=255, nullable=true)
     */
    private $mysqldb;

    /**
     * @var string $currentRevision
     *
     * @ORM\Column(name="mysqluser", type="string", length=255, nullable=true)
     */
    private $mysqluser;

    /**
     * @var string $currentRevision
     *
     * @ORM\Column(name="mysqlpw", type="string", length=255, nullable=true)
     */
    private $mysqlpw;

    /**
     * @var Application $application
     * @Assert\NotBlank(message="phing target could not be found")
     * @ORM\ManyToOne(targetEntity="PhingTarget")
     */
    private $phingTarget;

    /**
     * @ORM\Column(name="currentBranch", type="string", length=255, nullable=true)
     */
    private $currentBranch;

    /**
     * @var string $currentRevision
     *
     * @ORM\Column(name="currentRevision", type="string", length=255, nullable=true)
     */
    private $currentRevision;

    /**
     * @var string $approot
     * @ORM\Column(name="approot", type="string", length=255)
     */
    private $approot;

    /**
     * @var string $webroot
     * @ORM\Column(name="webroot", type="string", length=255)
     */
    private $webroot;

    /**
     * @var string $currentRevision
     * @todo should be an optionlist with following options (deployment target, copycontent target)
     * @ORM\Column(name="requiresRevision", type="boolean", nullable=true)
     */
    private $requiresRevision;


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
     * Set environment
     *
     * @param Environment $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Get environment
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set application
     *
     * @param Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
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
     * Set currentRevision
     *
     * @param string $currentRevision
     */
    public function setCurrentRevision($currentRevision)
    {
        $this->currentRevision = $currentRevision;
    }

    /**
     * Get currentRevision
     *
     * @return string 
     */
    public function getCurrentRevision()
    {
        return $this->currentRevision;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }


    public function __toString(){
        return $this->environment->getType().' ('.$this->environment->getHostname().')';
    }

    /**
     * @param string $mysqldb
     */
    public function setMysqldb($mysqldb)
    {
        $this->mysqldb = $mysqldb;
    }

    /**
     * @return string
     */
    public function getMysqldb()
    {
        return $this->mysqldb;
    }

    /**
     * @param string $mysqlpw
     */
    public function setMysqlpw($mysqlpw)
    {
        $this->mysqlpw = $mysqlpw;
    }

    /**
     * @return string
     */
    public function getMysqlpw()
    {
        return $this->mysqlpw;
    }

    /**
     * @param string $mysqluser
     */
    public function setMysqluser($mysqluser)
    {
        $this->mysqluser = $mysqluser;
    }

    /**
     * @return string
     */
    public function getMysqluser()
    {
        return $this->mysqluser;
    }

    /**
     * @param \Netvlies\PublishBundle\Entity\Application $phingTarget
     */
    public function setPhingTarget($phingTarget)
    {
        $this->phingTarget = $phingTarget;
    }

    /**
     * @return \Netvlies\PublishBundle\Entity\Application
     */
    public function getPhingTarget()
    {
        return $this->phingTarget;
    }

    public function setCurrentBranch($currentBranch)
    {
        $this->currentBranch = $currentBranch;
    }

    public function getCurrentBranch()
    {
        return $this->currentBranch;
    }

    /**
     * @param string $requiresRevision
     */
    public function setRequiresRevision($requiresRevision)
    {
        $this->requiresRevision = $requiresRevision;
    }

    /**
     * @return string
     */
    public function getRequiresRevision()
    {
        return $this->requiresRevision;
    }


    /**
     * @param string $approot
     */
    public function setApproot($approot)
    {
        $this->approot = $approot;
    }

    /**
     * @return string
     */
    public function getApproot()
    {
        return $this->approot;
    }

    /**
     * @param string $webroot
     */
    public function setWebroot($webroot)
    {
        $this->webroot = $webroot;
    }

    /**
     * @return string
     */
    public function getWebroot()
    {
        return $this->webroot;
    }
	
    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }	
}