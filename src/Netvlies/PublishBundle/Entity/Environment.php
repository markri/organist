<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\PublishBundle\Entity\Environment
 *
 * @ORM\Entity(repositoryClass="Netvlies\PublishBundle\Entity\EnvironmentRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="OTAPhost", columns={"type", "hostname"}), @ORM\UniqueConstraint(name="keyname", columns={"keyname"})})
 */
class Environment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank(message="type is required")
     * @Assert\Choice(choices = {"O", "T", "A", "P"}, message="Choose a valid servertype: O, T, A or P")
     */
    private $type;

    /**
     * @ORM\Column(name="hostname", type="string", length=255)
     * @Assert\NotBlank(message="hostname is required")
     */
    private $hostname;

    /**
     * @ORM\Column(name="sudoUser", type="string", length=255)
     * @Assert\NotBlank(message="sudoUser is required")
     */
    private $sudoUser = 'deploy';


    /**
     * @var string $homedirsBase
     * @Assert\Regex(pattern="#^/.*$#", match=true, message="Please use an absolute path")
     * @ORM\Column(name="homedirsBase", type="string", length=255, nullable=true)
     */
    private $homedirsBase = '/home';


    /**
     * @var string $keyname
     * @Assert\NotBlank(message="Unique keyname is required")
     * @ORM\Column(name="keyname", type="string", length=255)
     */
    private $keyname;


    /**
     * @var string $deploybridgecommand
     * @ORM\Column(name="deploybridgecommand", type="string", length=255)
     */
    private $deploybridgecommand;

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
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set hostname
     *
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set sudoUser
     *
     * @param string $sudoUser
     */
    public function setSudoUser($sudoUser)
    {
        $this->sudoUser = $sudoUser;
    }

    /**
     * Get sudoUser
     *
     * @return string 
     */
    public function getSudoUser()
    {
        return $this->sudoUser;
    }

    /**
     * @param string $homedirsBase
     */
    public function setHomedirsBase($homedirsBase)
    {
        $this->homedirsBase = $homedirsBase;
    }

    /**
     * @return string
     */
    public function getHomedirsBase()
    {
        return $this->homedirsBase;
    }

    /**
     * @param string $keyname
     */
    public function setKeyname($keyname)
    {
        $this->keyname = $keyname;
    }

    /**
     * @return string
     */
    public function getKeyname()
    {
        return $this->keyname;
    }

    /**
     * To identify this entity in forms
     */
    public function __toString(){
        return $this->getType().' ('.$this->getHostname().')';
    }

    /**
     * @param string $deploybridgecommand
     */
    public function setDeploybridgecommand($deploybridgecommand)
    {
        $this->deploybridgecommand = $deploybridgecommand;
    }

    /**
     * @return string
     */
    public function getDeploybridgecommand()
    {
        return $this->deploybridgecommand;
    }
}