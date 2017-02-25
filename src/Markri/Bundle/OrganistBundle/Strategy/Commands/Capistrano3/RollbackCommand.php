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
use Markri\Bundle\OrganistBundle\Entity\Target;
use Markri\Bundle\OrganistBundle\Entity\UserFile;

/**
 * Class RollbackCommand
 * @package Markri\Bundle\OrganistBundle\Action\Capistrano3
 * @todo fix Capistrano 3 command
 */
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
     * @return string
     */
    public function getRevision()
    {
        return '';
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

        $updateVersionScript = $this->getUpdateVersionScript();


        //@todo there is dtap and otap, otap is still there for BC
        return trim(preg_replace('/\s\s+/', ' ', "
            cap ".$this->target->getEnvironment()->getType()." deploy:rollback
            -Sproject='".$this->application->getName()."'
            -Sapptype='".$this->application->getApplicationType()."'
            -Sappkey='".$this->application->getKeyName()."'
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
            -Ssshport='".$this->target->getEnvironment()->getPort()."'
            -Sotap='".$this->target->getEnvironment()->getType()."'
            -Sdtap='".$this->target->getEnvironment()->getType()."'
            -Suserfiles='".implode(',', $files)."'
            -Suserdirs='".implode(',', $dirs)."'
            -Svhostaliases='".implode(',', $vhostAliases)."'".
           $updateVersionScript
           )
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
