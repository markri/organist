<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\PublishBundle\Entity\ConsoleLog
 *
 * @ORM\Table()
 * @ORM\Entity
  */
class ConsoleLog
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
     * @var string $host
     * @ORM\Column(name="host", type="string", length=255, nullable=true)
     */
    private $host;

    /**
     * @var string $type
     * @ORM\Column(name="type", type="string", length=1, nullable=true)
     */
    private $type;

    /**
     * @var DateTime $datetimeStart
     * @ORM\Column(name="datetimestart", type="datetime")
     */
    private $datetimeStart;


    /**
     * @var DateTime $datetimeEnd
     * @ORM\Column(name="datetimeend", type="datetime", nullable=true)
     */
    private $datetimeEnd;


    /**
     * @var string $user
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var string $key
     * @ORM\Column(name="uid", type="string", length=255)
     */
    private $uid;

    /**
     * @var string $command
     * @ORM\Column(name="command", type="text")
     */
    private $command;


    /**
     * @var string $log
     * @ORM\Column(name="log", type="text", nullable=true)
     */
    private $log;

    /**
     * @var string $exitcode
     * @ORM\Column(name="exitcode", type="string", nullable=true)
     */
    private $exitCode;


    /**
     * @todo soft link So targets can still be deleted without constraints to consoleLog, to set null
     * This variable is used as a temporary backreference to update current status (revision etc)
     * @var int $targetId
     * @ORM\Column(name="targetid", type="integer", nullable=true)
     */
    private $targetId;


    /**
     * @var string $revision
     * @ORM\Column(name="revision", type="string", length=255, nullable=true)
     */
    private $revision;


    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param \Netvlies\PublishBundle\Entity\DateTime $datetimeStart
     */
    public function setDatetimeStart($datetimeStart)
    {
        $this->datetimeStart = $datetimeStart;
    }

    /**
     * @return \Netvlies\PublishBundle\Entity\DateTime
     */
    public function getDatetimeStart()
    {
        return $this->datetimeStart;
    }


    /**
     * @param \Netvlies\PublishBundle\Entity\DateTime $datetimeEnd
     */
    public function setDatetimeEnd($datetimeEnd)
    {
        $this->datetimeEnd = $datetimeEnd;
    }

    /**
     * @return \Netvlies\PublishBundle\Entity\DateTime
     */
    public function getDatetimeEnd()
    {
        return $this->datetimeEnd;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
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
     * @param string $key
     */
    public function setUid($key)
    {
        $this->uid = $key;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $exitCode
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
    }

    /**
     * @return string
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @param int $deploymentId
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;
    }

    /**
     * @return int
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setRevision($revision)
    {
        $this->revision = $revision;
    }

    public function getRevision()
    {
        return $this->revision;
    }
}