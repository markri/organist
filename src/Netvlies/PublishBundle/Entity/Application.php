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
 * @todo custom parameters can be added which will be passed allong when executing something in consolecontroller
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
     * @Assert\Regex(pattern="/^git@bitbucket.org:.*?.git$/", match=true, message="Use GIT SSH connection string from Bitbucket")
     * @ORM\Column(name="gitrepoSSH", type="string", length=255, nullable=true)
     */
    private $gitrepoSSH;

    /**
     * @ORM\Column(name="repokey", type="string", length=255, nullable=true)
     */
    private $repokey;

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
     * @var string $branchToFollow
     * @ORM\Column(name="branchtofollow", type="string", length=255, nullable=true)
     */
    private $branchToFollow;

    /**
     * @var string $branchToFollow
     * @ORM\Column(name="referencetofollow", type="string", length=255, nullable=true)
     */
    private $referenceToFollow;


    //private $referenceToDeploy;



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
     * Set gitrepoSSH
     *
     * @param string $gitrepoSSH
     */
    public function setGitrepoSSH($gitrepoSSH)
    {
        $this->gitrepoSSH = $gitrepoSSH;
    }

    /**
     * Get gitrepoSSH
     *
     * @return string 
     */
    public function getGitrepoSSH()
    {
        return $this->gitrepoSSH;
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


    public function setMysqlpw($mysqlpw)
    {
        $this->mysqlpw = $mysqlpw;
    }

    public function getMysqlpw()
    {
        return $this->mysqlpw;
    }

//    public function setReferenceToDeploy($referenceToDeploy)
//    {
//        $this->referenceToDeploy = $referenceToDeploy;
//    }
//
//    public function getReferenceToDeploy()
//    {
//        return $this->referenceToDeploy;
//    }


    public function setRepokey($repokey)
    {
        $this->repokey = $repokey;
    }

    public function getRepokey()
    {
        return $this->repokey;
    }

    public function setBranchToFollow($branchToFollow)
    {
        $this->branchToFollow = $branchToFollow;
    }

    public function getBranchToFollow()
    {
        return $this->branchToFollow;
    }

    /**
     * @param string $referenceToFollow
     */
    public function setReferenceToFollow($referenceToFollow)
    {
        $this->referenceToFollow = $referenceToFollow;
    }

    /**
     * @return string
     */
    public function getReferenceToFollow()
    {
        return $this->referenceToFollow;
    }


}