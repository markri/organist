<?php

namespace Netvlies\Bundle\MigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Target
 *
 * @ORM\Table(name="Target")
 * @ORM\Entity
 */
class Target
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="primaryDomain", type="string", length=255, nullable=true)
     */
    private $primarydomain;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="mysqldb", type="string", length=255, nullable=true)
     */
    private $mysqldb;

    /**
     * @var string
     *
     * @ORM\Column(name="mysqluser", type="string", length=255, nullable=true)
     */
    private $mysqluser;

    /**
     * @var string
     *
     * @ORM\Column(name="mysqlpw", type="string", length=255, nullable=true)
     */
    private $mysqlpw;

    /**
     * @var string
     *
     * @ORM\Column(name="currentBranch", type="string", length=255, nullable=true)
     */
    private $currentbranch;

    /**
     * @var string
     *
     * @ORM\Column(name="currentRevision", type="string", length=255, nullable=true)
     */
    private $currentrevision;

    /**
     * @var string
     *
     * @ORM\Column(name="approot", type="string", length=255, nullable=false)
     */
    private $approot;

    /**
     * @var string
     *
     * @ORM\Column(name="webroot", type="string", length=255, nullable=false)
     */
    private $webroot;

    /**
     * @var string
     *
     * @ORM\Column(name="caproot", type="string", length=255, nullable=true)
     */
    private $caproot;

    /**
     * @var \Application
     *
     * @ORM\ManyToOne(targetEntity="Application")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     * })
     */
    private $application;

    /**
     * @var Environment
     *
     * @ORM\ManyToOne(targetEntity="Environment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="environment_id", referencedColumnName="id")
     * })
     */
    private $environment;

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getApproot()
    {
        return $this->approot;
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
    public function getCurrentbranch()
    {
        return $this->currentbranch;
    }

    /**
     * @return string
     */
    public function getCurrentrevision()
    {
        return $this->currentrevision;
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getMysqldb()
    {
        return $this->mysqldb;
    }

    /**
     * @return string
     */
    public function getMysqlpw()
    {
        return $this->mysqlpw;
    }

    /**
     * @return string
     */
    public function getMysqluser()
    {
        return $this->mysqluser;
    }

    /**
     * @return string
     */
    public function getPrimarydomain()
    {
        return $this->primarydomain;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getWebroot()
    {
        return $this->webroot;
    }



}
