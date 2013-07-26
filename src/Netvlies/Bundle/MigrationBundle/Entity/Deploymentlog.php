<?php

namespace Netvlies\Bundle\MigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deploymentlog
 *
 * @ORM\Table(name="DeploymentLog")
 * @ORM\Entity
 */
class Deploymentlog
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
     * @ORM\Column(name="user", type="string", length=255, nullable=false)
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="targetid", type="integer", nullable=true)
     */
    private $targetid;

    /**
     * @var string
     *
     * @ORM\Column(name="uid", type="string", length=255, nullable=false)
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255, nullable=true)
     */
    private $host;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=1, nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetimestart", type="datetime", nullable=false)
     */
    private $datetimestart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetimeend", type="datetime", nullable=true)
     */
    private $datetimeend;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="text", nullable=false)
     */
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="exitcode", type="string", length=255, nullable=true)
     */
    private $exitcode;

    /**
     * @var string
     *
     * @ORM\Column(name="log", type="text", nullable=true)
     */
    private $log;

    /**
     * @var string
     *
     * @ORM\Column(name="revision", type="string", length=255, nullable=true)
     */
    private $revision;

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeend()
    {
        return $this->datetimeend;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimestart()
    {
        return $this->datetimestart;
    }

    /**
     * @return string
     */
    public function getExitcode()
    {
        return $this->exitcode;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
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
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @return int
     */
    public function getTargetid()
    {
        return $this->targetid;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }




}
