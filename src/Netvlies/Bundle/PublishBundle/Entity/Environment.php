<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\Bundle\PublishBundle\Entity\Environment
 *
 * @ORM\Entity(repositoryClass="Netvlies\Bundle\PublishBundle\Entity\EnvironmentRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="OTAPhost", columns={"type", "hostname"}), @ORM\UniqueConstraint(name="keyname", columns={"keyname"})})
 */
class Environment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $keyName
     * @Assert\NotBlank(message="Unique keyname is required")
     * @ORM\Column(name="keyName", type="string", length=255)
     */
    protected $keyName;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank(message="type is required")
     * @Assert\Choice(choices = {"O", "T", "A", "P"}, message="Choose a valid servertype: O, T, A or P")
     */
    protected $type;

    /**
     * @ORM\Column(name="hostname", type="string", length=255)
     * @Assert\NotBlank(message="hostname is required")
     */
    protected $hostname;


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
     * @param string $keyName
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName;
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
     * To identify this entity in forms
     */
    public function __toString()
    {
        return $this->getType().' ('.$this->getHostname().')';
    }
}