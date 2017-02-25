<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Strategy\Commands\Capistrano3;

use Markri\Bundle\OrganistBundle\Strategy\Commands\BaseUpdateCommand;
use Markri\Bundle\OrganistBundle\Entity\Application;
use Markri\Bundle\OrganistBundle\Entity\DomainAlias;
use Markri\Bundle\OrganistBundle\Entity\Target;
use Markri\Bundle\OrganistBundle\Entity\UserFile;
use Markri\Bundle\OrganistBundle\Versioning\VersioningInterface;

/**
 * Class DeployCommand
 * @package Markri\Bundle\OrganistBundle\Action\Capistrano3
 *
 */
class DeployCommand extends BaseUpdateCommand
{
    /**
     * @var Application $application
     */
    protected $application;

    /**
     * @var Target $target
     */
    protected $target;

    /**
     * @var string $revision
     */
    protected $revision;

    /**
     * @var VersioningInterface $versioningService
     */
    protected $versioningService;

    /**
     * @param \Markri\Bundle\OrganistBundle\Entity\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return \Markri\Bundle\OrganistBundle\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param string $revision
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @param \Markri\Bundle\OrganistBundle\Entity\Target $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return \Markri\Bundle\OrganistBundle\Entity\Target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param VersioningInterface $versioningService
     */
    public function setVersioningService(VersioningInterface $versioningService)
    {
        $this->versioningService = $versioningService;
    }


    public function getCommand()
    {
        $userFiles = $this->application->getUserFiles();
        $files = array();
        $dirs = array();
        $vhostAliases = array();

        $keyForwardOpen = '';
        $keyForwardClose = '';

        foreach($userFiles as $userFile){
            /**
             * @var UserFile $userFile
             */
            if($userFile->getType()=='F'){

                $files[] = $userFile->getPath();
            }
            else{
                $dirs[] = $userFile->getPath();
            }
        }

        foreach($this->target->getDomainAliases() as $alias){
            /**
             * @var DomainAlias $alias
             */
            $vhostAliases[] = $alias->getAlias();
        }

        if($this->versioningService->getPrivateKey()){
            // Forward optional keys for versioning. Set SSH-agent timeout for 2 hours to keep process list clear
            $keyForwardOpen ='eval `ssh-agent -t 7200` && ';
            $keyForwardOpen.='`ssh-add '.$this->versioningService->getPrivateKey().'` && ';

            // And kill them as well
            $keyForwardClose =' && ssh-agent -k > /dev/null 2>&1 && ';
            $keyForwardClose.='unset SSH_AGENT_PID && ';
            $keyForwardClose.='unset SSH_AUTH_SOCK';
        }

        $updateVersionScript = $this->getUpdateVersionScript();

        $command =  trim(preg_replace('/\s\s+/', ' ', "
            $keyForwardOpen
            cap ".$this->target->getEnvironment()->getType()." deploy
            project='".$this->application->getName()."'
            apptype='".$this->application->getApplicationType()."'
            appkey='".$this->application->getKeyName()."'
            gitrepo='".$this->application->getScmUrl()."'
            repositorypath='".$this->getRepositoryPath()."'
            sudouser='deploy'
            revision='".$this->revision."'
            username='".$this->target->getUsername()."'
            mysqldb='".$this->target->getMysqldb()."'
            mysqluser='".$this->target->getMysqluser()."'
            mysqlpw='".$this->target->getMysqlpw()."'
            webroot='".$this->target->getWebroot()."'
            approot='".$this->target->getApproot()."'
            caproot='".$this->target->getCaproot()."'
            primarydomain='".$this->target->getPrimaryDomain()."'
            homedirsBase='/home'
            hostname='".$this->target->getEnvironment()->getHostname()."'
            sshport='".$this->target->getEnvironment()->getPort()."'
            dtap='".$this->target->getEnvironment()->getType()."'
            userfiles='".implode(',', $files)."'
            userdirs='".implode(',', $dirs)."'
            vhostaliases='".implode(',', $vhostAliases)."'
            $updateVersionScript
            $keyForwardClose
            "));

        if ($this->application->getScmService() != 'jenkins') {
            //@todo should be done through versionnigService
            $command = "git checkout '" . $this->revision . "' &&" . $command;
        } else {
            $this->versioningService->checkoutRevision($this->application, $this->revision);
        }

        return $command;
    }


    /**
     * @return string
     */
    public function getRepositoryPath()
    {
        return $this->versioningService->getRepositoryPath($this->getApplication());
    }

    /**
     * Must return descriptive label for command type
     * @return string
     */
    public function getLabel()
    {
        return 'Capistrano deployment';
    }
}
