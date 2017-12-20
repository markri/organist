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

use Markri\Bundle\OrganistBundle\Strategy\Commands\CommandTargetInterface;
use Markri\Bundle\OrganistBundle\Entity\Application;
use Markri\Bundle\OrganistBundle\Entity\Target;
use Markri\Bundle\OrganistBundle\Entity\UserFile;

/**
 * Class InitCommand
 * @package Markri\Bundle\OrganistBundle\Action\Capistrano3
 */
class InitCommand implements CommandTargetInterface
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
     * @var string $repositoryPath
     */
    protected $repositoryPath;

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



    public function getCommand()
    {
        $userFiles = $this->application->getUserFiles();
        $files = array();
        $dirs = array();
        $vhostAliases = array();

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

        $command = trim(preg_replace('/\s\s+/', ' ', "
            git checkout master &&
            cap ".$this->target->getEnvironment()->getType()." deploy:setup
            project='".$this->application->getName()."'
            apptype='".$this->application->getApplicationType()."'
            appkey='".$this->application->getKeyName()."'
            gitrepo='".$this->application->getScmUrl()."'
            repositorypath='".$this->repositoryPath."'
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
            otap='".$this->target->getEnvironment()->getType()."'
            userfiles='".implode(',', $files)."'
            userdirs='".implode(',', $dirs)."'
            Svhostaliases='".implode(',', $vhostAliases)."'"
        ));


        return $command;
    }

    /**
     * @return string
     */
    public function getRepositoryPath()
    {
        return $this->repositoryPath;
    }

    /**
     * @return mixed
     */
    public function setRepositoryPath($repositoryPath)
    {
        $this->repositoryPath = $repositoryPath;
    }

    /**
     * Must return descriptive label for command type
     * @return string
     */
    public function getLabel()
    {
        return 'Capistrano setup';
    }


}