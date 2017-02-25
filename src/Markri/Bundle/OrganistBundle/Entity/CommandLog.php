<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Markri\Bundle\OrganistBundle\Entity\Command
 * @ORM\Entity(repositoryClass="Markri\Bundle\OrganistBundle\Entity\CommandLogRepository")
 * @ORM\Table()
  */
class CommandLog
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
     * @var \DateTime $datetimeStart
     * @ORM\Column(name="datetimestart", type="datetime")
     */
    private $datetimeStart;


    /**
     * @var \DateTime $datetimeEnd
     * @ORM\Column(name="datetimeend", type="datetime", nullable=true)
     */
    private $datetimeEnd;


    /**
     * @var string $user
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

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
     * This variable is used as a  backreference
     * @var Target $target
     * @ORM\ManyToOne(targetEntity="Target")
     */
    private $target;


    /**
     * This variable is used as a  backreference. not required, because it is set by calling setTarget method. But  a
     * command can have an application without a target.
     * @var Application $target
     * @ORM\ManyToOne(targetEntity="Application")
     */
    private $application;


    /**
     * @var string
     * @ORM\Column(name="commandLabel", type="string", nullable=true)
     */
    private $commandLabel;


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
     * @param \DateTime $datetimeStart
     */
    public function setDatetimeStart($datetimeStart)
    {
        $this->datetimeStart = $datetimeStart;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeStart()
    {
        return $this->datetimeStart;
    }


    /**
     * @param \DateTime $datetimeEnd
     */
    public function setDatetimeEnd($datetimeEnd)
    {
        $this->datetimeEnd = $datetimeEnd;
    }

    /**
     * @return \DateTime
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
     * @param Target $target
     */
    public function setTarget($target)
    {
        $this->target = $target;

        // Be sure that this is correctly set, so overrule any existing value
        // only on commands that have a target
        if(!empty($target)){
            $this->application = $target->getApplication();
        }
    }

    /**
     * @return Target
     */
    public function getTarget()
    {
        return $this->target;
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


    /**
     * @param string $commandLabel
     */
    public function setCommandLabel($commandLabel)
    {
        $this->commandLabel = $commandLabel;
    }

    /**
     * @return string
     */
    public function getCommandLabel()
    {
        return $this->commandLabel;
    }

    /**
     * @param \Markri\Bundle\OrganistBundle\Entity\Application $application
     */
    public function setApplication($application)
    {
        if($this->target && $this->target->getApplication() != $application){
            // Be sure that target is matching application if these are different
            $this->setTarget(null);
        }

        $this->application = $application;
    }

    /**
     * @return \Markri\Bundle\OrganistBundle\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }



}
