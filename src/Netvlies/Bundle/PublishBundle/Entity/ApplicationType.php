<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\Bundle\PublishBundle\Entity\ApplicationType
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ApplicationType
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="keyname", type="string", length=255)
     */
    protected $keyName;

    /**
     * @ORM\Column(name="displayName", type="string", length=255)
     */
    protected $displayName;


    /**
     * @ORM\Column(name="initScript", type="string", length=255)
     */
    protected $initScript;

    /**
     * @ORM\OneToMany(targetEntity="Command", mappedBy="applicationType")
     */
    protected $commands;



    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    public function getKeyName()
    {
        return $this->keyName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setInitScript($initScript)
    {
        $this->initScript = $initScript;
    }

    public function getInitScript()
    {
        return $this->initScript;
    }

    public function getDeployCommand()
    {
        foreach($this->commands as $command){
            /**
             * @var $command Command
             */
            if($command->getDisplayName()=='Deploy'){
                return $command->getCommand();
            }
        }

        return '';
    }

}