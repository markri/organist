<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Netvlies\PublishBundle\Entity\ScriptBuilder;

/**
 * Netvlies\PublishBundle\Entity\Application
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Netvlies\PublishBundle\Entity\ApplicationRepository")
 */
class Application
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="name is required")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

   /**
    * @Assert\NotBlank(message="customer is required")
    * @ORM\Column(name="customer", type="string", length=255)
    */
    private $customer;

    /**
     * @var ApplicationType $type
     * @ORM\ManyToOne(targetEntity="ApplicationType")
     */
    private $type;

    /**
     * @ORM\Column(name="scmURL", type="string", length=255, nullable=true)
     */
    private $scmURL;

    /**
     * @ORM\Column(name="scmKey", type="string", length=255, nullable=true)
     */
    private $scmKey;

    /**
     * @var string $scmService
     * @ORM\Column(name="scmService", type="string", length=255, nullable=true)
     */
    private $scmService;

    /**
     * @ORM\Column(name="mysqlpw", type="string", length=255, nullable=true)
     */
    private $mysqlpw;

    /**
     * @var object $userFiles
     * @ORM\OneToMany(targetEntity="UserFiles", mappedBy="application")
     */
    private $userFiles;


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
     * Set SCM URL
     * @param string $scmURL
     */
    public function setScmURL($scmURL)
    {
        $this->scmURL = $scmURL;
    }

    /**
     * Get SCM URL
     * @return string 
     */
    public function getScmURL()
    {
        return $this->scmURL;
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
     * @param $mysqlpw
     */
    public function setMysqlpw($mysqlpw)
    {
        $this->mysqlpw = $mysqlpw;
    }

    /**
     * @return mixed
     */
    public function getMysqlpw()
    {
        return $this->mysqlpw;
    }

    /**
     * @todo is this even needed when we have scmUrl?
     * @param $repokey
     */
    public function setScmKey($scmKey)
    {
        $this->scmKey = $scmKey;
    }

    /**
     * @return mixed
     */
    public function getScmKey()
    {
        return $this->scmKey;
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
     * @param $repoBasePath
     * @return string
     */
    public function getAbsolutePath($repoBasePath)
    {
        return $repoBasePath.'/'.$this->getScmKey();
    }


}