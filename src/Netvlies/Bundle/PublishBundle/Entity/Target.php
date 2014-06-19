<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Netvlies\Bundle\PublishBundle\Entity\TargetRepository")
 */
class Target
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank(message="environment is required/key could not be found")
     * @ORM\ManyToOne(targetEntity="Environment")
     */
    protected $environment;

    /**
     * @var string $label
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    protected $label;

    /**
     * @var Application $application
     * @Assert\NotBlank(message="application is required/key could not be found")
     * @ORM\ManyToOne(targetEntity="Application", inversedBy="targets")
     */
    protected $application;

    /**
     * @ORM\Column(name="currentBranch", type="string", length=255, nullable=true)
     */
    protected $lastDeployedBranch;

    /**
     * @var string $currentRevision
     * @ORM\Column(name="currentRevision", type="string", length=255, nullable=true)
     */
    protected $lastDeployedRevision;

    /**
     * @ORM\Column(name="currentTag", type="string", length=255, nullable=true)
     */
    protected $lastDeployedTag;

    /**
     * @var string $approot where application is installed/runnend from
     * @ORM\Column(name="approot", type="string", length=255, nullable=true)
     */
    protected $approot;

    /**
     * @var string $webroot where application webroot is served from, should be subdir of approot
     * @ORM\Column(name="webroot", type="string", length=255, nullable=true)
     */
    protected $webroot;

    /**
     * @var string $caproot base path where capistrano structure is stored
     * @ORM\Column(name="caproot", type="string", length=255, nullable=true)
     */
    protected $caproot;

    /**
     * @ORM\Column(name="username", type="string", length=255)
     */
    protected $username;


    /**
     * @var string $currentRevision
     *
     * @ORM\Column(name="mysqldb", type="string", length=255, nullable=true)
     */
    protected $mysqldb;

    /**
     * @var string $currentRevision
     *
     * @ORM\Column(name="mysqluser", type="string", length=255, nullable=true)
     */
    protected $mysqluser;

    /**
     * @var string $currentRevision
     *
     * @ORM\Column(name="mysqlpw", type="string", length=255, nullable=true)
     */
    protected $mysqlpw;


    /**
     * @ORM\Column(name="primaryDomain", type="string", length=255, nullable=true)
     */
    protected $primaryDomain;


    /**
     * @ORM\OneToMany(targetEntity="DomainAlias", mappedBy="target")
     */
    protected $domainAliases;


    /**
     * @ORM\Column(name="inactive", type="boolean")
     */
    protected $inactive = 0;

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
     * @return \Netvlies\Bundle\PublishBundle\Entity\Application
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
    public function setLastDeployedRevision($lastDeployedRevision)
    {
        $this->lastDeployedRevision = $lastDeployedRevision;
    }

    /**
     * Get currentRevision
     *
     * @return string
     */
    public function getLastDeployedRevision()
    {
        return $this->lastDeployedRevision;
    }

    /**
     * @param mixed $lastDeployedTag
     */
    public function setLastDeployedTag($lastDeployedTag)
    {
        $this->lastDeployedTag = $lastDeployedTag;
    }

    /**
     * @return mixed
     */
    public function getLastDeployedTag()
    {
        return $this->lastDeployedTag;
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

    /**
     * @param $lastDeployedBranch
     */
    public function setLastDeployedBranch($lastDeployedBranch)
    {
        $this->lastDeployedBranch = $lastDeployedBranch;
    }

    /**
     * @return string
     */
    public function getLastDeployedBranch()
    {
        return $this->lastDeployedBranch;
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
     * @param $label string
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

    /**
     * @param  $caproot
     */
    public function setCaproot($caproot)
    {
        $this->caproot = $caproot;
    }

    /**
     * @return string
     */
    public function getCaproot()
    {
        return $this->caproot;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->label;
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
     * @param mixed $primaryDomain
     */
    public function setPrimaryDomain($primaryDomain)
    {
        $this->primaryDomain = $primaryDomain;
    }

    /**
     * @return mixed
     */
    public function getPrimaryDomain()
    {
        return $this->primaryDomain;
    }


    /**
     * @param UserFile $alias
     */
    public function addDomainAlias(DomainAlias $alias)
    {
        if(!$this->domainAliases){
            $this->domainAliases = new \Doctrine\Common\Collections\ArrayCollection();
        }

        $alias->setTarget($this);
        $this->domainAliases->add($alias);
    }

    public function removeDomainAlias($alias)
    {
        if(!$this->domainAliases){
            return;
        }

        $this->domainAliases->removeElement($alias);
    }

    /**
     * @return mixed
     */
    public function getDomainAliases()
    {
        return $this->domainAliases;
    }


    /**
     * @param mixed $inactive
     */
    public function setInactive($inactive)
    {
        $this->inactive = $inactive;
    }

    /**
     * @return mixed
     */
    public function getInactive()
    {
        return $this->inactive;
    }
}