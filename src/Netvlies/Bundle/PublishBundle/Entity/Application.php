<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\Bundle\PublishBundle\Entity\Application
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="keyName", columns={"keyName"})})
 * @ORM\Entity(repositoryClass="Netvlies\Bundle\PublishBundle\Entity\ApplicationRepository")
 */
class Application
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * keyname is used for db user, db name, environment user, etc
     * @ORM\Column(name="keyName", type="string", length=16)
     * @Assert\Length(max=16)
     */
    protected $keyName;

    /**
     * @Assert\NotBlank(message="name is required")
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

   /**
    * @Assert\NotBlank(message="customer is required")
    * @ORM\Column(name="customer", type="string", length=255)
    */
    protected $customer;

    /**
     * @var ApplicationType $type
     * @ORM\ManyToOne(targetEntity="ApplicationType")
     */
    protected $type;

    /**
     * @var string $scmService
     * @ORM\Column(name="scmService", type="string", length=255, nullable=true)
     */
    protected $scmService;

    /**
     * @var string $scmUrl
     * @ORM\Column(name="scmUrl", type="string", length=255, nullable=true)
     */
    protected $scmUrl;

    /**
     * @var object $userFiles
     * @ORM\OneToMany(targetEntity="UserFile", mappedBy="application", cascade={"persist", "remove"})
     */
    protected $userFiles;

    /**
     * @var object $targets
     * @ORM\OneToMany(targetEntity="Target", mappedBy="application")
     */
    protected $targets;

    /**
     * @todo implement decent commandlog
     */
    protected $commandLog;



    public function __construct()
    {
        $this->userFiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param ApplicationType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return ApplicationType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set userFiles
     *
     * @param object $userFiles
     */
    public function setUserFiles($userFiles)
    {
        $this->userFiles = $userFiles;
    }

    /**
     * @param UserFile $file
     */
    public function addUserFile(UserFile $file)
    {
        $this->userFiles[] = $file;
    }

    /**
     * Get userFiles
     *
     * @return object
     */
    public function getUserFiles()
    {
        return $this->userFiles;
    }

    /**
     * @param $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return string
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param $repokey
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * @param string $scmService
     */
    public function setScmService($scmService)
    {
        $this->scmService = $scmService;
    }

    /**
     * @return string
     */
    public function getScmService()
    {
        return $this->scmService;
    }

    /**
     * @param string $scmUrl
     */
    public function setScmUrl($scmUrl)
    {
        $this->scmUrl = $scmUrl;
    }

    /**
     * @return string
     */
    public function getScmUrl()
    {
        return $this->scmUrl;
    }

    /**
     * @param object $targets
     */
    public function setTargets($targets)
    {
        $this->targets = $targets;
    }

    /**
     * @return object
     */
    public function getTargets()
    {
        return $this->targets;
    }

    public function setCommandLog($commandLog)
    {
        $this->commandLog = $commandLog;
    }

    public function getCommandLog()
    {
        return $this->commandLog;
    }

}
