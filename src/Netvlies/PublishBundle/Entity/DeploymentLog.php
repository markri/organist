<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\PublishBundle\Entity\DeploymentLog
 *
 * @ORM\Table()
 * @ORM\Entity
  */
class DeploymentLog
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
     * @ORM\Column(name="host", type="string", length=255)
     */
    private $host;

    /**
     * @var string $type
     * @ORM\Column(name="type", type="string", length=1)
     */
    private $type;

    /**
     * @var DateTime $datetimeStart
     * @ORM\Column(name="datetimestart", type="datetime")
     */
    private $datetimeStart;


    /**
     * @var DateTime $datetimeEnd
     * @ORM\Column(name="datetimeend", type="datetime", nullable="true")
     */
    private $datetimeEnd;

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
     * @ORM\Column(name="log", type="text", nullable="true")
     */
    private $log;

    /**
     * @var string $exitcode
     * @ORM\Column(name="exitcode", type="string", nullable="true")
     */
    private $exitCode;



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
}