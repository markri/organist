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

class DeployCommand implements CommandInterface {


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



    public function getCommand()
    {
        $userFiles = $this->application->getUserFiles();
        $files = array();
        $dirs = array();

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

        return trim(preg_replace('/\s\s+/', ' ', "
            git checkout ".$this->revision." &&
            cap ".$this->target->getEnvironment()->getType()." deploy:update
            -Sproject='".$this->application->getName()."'
            -Sgitrepo='".$this->application->getScmUrl()."'
            -Srepositorypath='".$this->repositoryPath."'
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
            -Sbridgebin='/home/hosting-ftp/deploy/deploy_bridge'
            -Suserfiles='".implode(',', $files)."'
            -Suserdirs='".implode(',', $dirs)."'"));
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


}