<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Action;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishBundle\Entity\UserFile;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;

class RollbackCommand extends BaseUpdateCommand
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
     * @return string
     */
    public function getRevision()
    {
        return '';
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

        $updateVersionScript = $this->getUpdateVersionScript();

        //@todo there is dtap and otap, otap is still there for BC
        return trim(preg_replace('/\s\s+/', ' ', "
            cap ".$this->target->getEnvironment()->getType()." deploy:rollback
            -Sproject='".$this->application->getName()."'
            -Sgitrepo='".$this->application->getScmUrl()."'
            -Srepositorypath='".$this->repositoryPath."'
            -Ssudouser='deploy'
            -Srevision='".$this->getRevision()."'
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
            -Suserfiles='".implode(',', $files)."'
            -Suserdirs='".implode(',', $dirs)."'".
            $updateVersionScript)
        );
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
        return 'Capistrano rollback';
    }


}