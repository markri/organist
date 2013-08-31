<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OrderBy({"type" = "ASC", "path" = "ASC"})
     */
    protected $userFiles;

    /**
     * @var object $targets
     * @ORM\OneToMany(targetEntity="Target", mappedBy="application")
     */
    protected $targets;

    /**
     * @var string $applicationType
     * @ORM\Column(name="applicationType", type="string", length=255)
     */
    protected $applicationType;



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
     * @param UserFile $file
     */
    public function addUserFile(UserFile $file)
    {
        $file->setApplication($this);
        $this->userFiles->add($file);
    }

    public function removeUserFile($userFile)
    {
        $this->userFiles->removeElement($userFile);
    }

    /**
     * Get userFiles
     *
     * @return ArrayCollection
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

    /**
     * @param string $applicationType
     */
    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
    }

    /**
     * @return string
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }
}