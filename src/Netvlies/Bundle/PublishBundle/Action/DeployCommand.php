<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 7/17/13
 * Time: 4:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Action;


use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishBundle\Entity\UserFile;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;

class DeployCommand implements CommandTargetInterface {


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
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return \Netvlies\Bundle\PublishBundle\Entity\Application
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
     * @param \Netvlies\Bundle\PublishBundle\Entity\Target $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return \Netvlies\Bundle\PublishBundle\Entity\Target
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

        if($this->versioningService->getPrivateKey()){
            // Forward optional keys for versioning
            $keyForwardOpen ='eval `ssh-agent` && ';
            $keyForwardOpen.='`ssh-add '.$this->versioningService->getPrivateKey().'` && ';

            // And kill them as well
            $keyForwardClose =' && ssh-agent -k > /dev/null 2>&1 && ';
            $keyForwardClose.='unset SSH_AGENT_PID && ';
            $keyForwardClose.='unset SSH_AUTH_SOCK';
        }

        //@todo eliminate bridgebin by using parameter bundle
        //@todo there is dtap and otap, otap is still there for BC
        return trim(preg_replace('/\s\s+/', ' ', "
            $keyForwardOpen
            git checkout '".$this->revision."' &&
            cap ".$this->target->getEnvironment()->getType()." deploy:update
            -Sproject='".$this->application->getName()."'
            -Sgitrepo='".$this->application->getScmUrl()."'
            -Srepositorypath='".$this->getRepositoryPath()."'
            -Ssudouser='deploy'
            -Srevision='".$this->revision."'
            -Susername='".$this->target->getUsername()."'
            -Smysqldb='".$this->target->getMysqldb()."'
            -Smysqluser='".$this->target->getMysqluser()."'
            -Smysqlpw='".$this->target->getMysqlpw()."'
            -Swebroot='".$this->target->getWebroot()."'
            -Sapproot='".$this->target->getApproot()."'
            -Scaproot='".$this->target->getCaproot()."'
            -Sprimarydomain='".$this->target->getPrimaryDomain()."'
            -ShomedirsBase='/home'
            -Shostname='".$this->target->getEnvironment()->getHostname()."'
            -Sotap='".$this->target->getEnvironment()->getType()."'
            -Sdtap='".$this->target->getEnvironment()->getType()."'
            -Sbridgebin='/home/hosting-ftp/deploy/deploy_bridge'
            -Suserfiles='".implode(',', $files)."'
            -Suserdirs='".implode(',', $dirs)."'
            $keyForwardClose
            "));
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