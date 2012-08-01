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
     * @ORM\Column(name="sudoUser", type="string", length=255)
     * @Assert\NotBlank(message="sudoUser is required")
     */
    protected $sudoUser = 'deploy';

    /**
     * @var string $sshPort
     * @ORM\Column(name="sshPort", type="string", length=4)
     */
    protected $sshPort;

    /**
     * @ORM\Column(name="mysqlAdminUser", type="string", length=255)
     */
    protected $mysqlAdminUser = 'root';

    /**
     * @ORM\Column(name="mysqlAdminPassword", type="string", length=255)
     */
    protected $mysqlAdminPassword;




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
     * @param $sshPort
     */
    public function setSshPort($sshPort)
    {
        $this->sshPort = $sshPort;
    }

    /**
     * @return string
     */
    public function getSshPort()
    {
        return $this->sshPort;
    }

    /**
     * @param string $mysqlAdminUser
     */
    public function setMysqlAdminUser($mysqlAdminUser)
    {
        $this->mysqlAdminUser = $mysqlAdminUser;
    }

    /**
     * @return string
     */
    public function getMysqlAdminUser()
    {
        return $this->mysqlAdminUser;
    }

    /**
     * @param string $mysqlAdminPassword
     */
    public function setMysqlAdminPassword($mysqlAdminPassword)
    {
        $this->mysqlAdminPassword = $mysqlAdminPassword;
    }

    /**
     * @return string
     */
    public function getMysqlAdminPassword()
    {
        return $this->mysqlAdminPassword;
    }

    /**
     * To identify this entity in forms
     */
    public function __toString()
    {
        return $this->getType().' ('.$this->getHostname().')';
    }
}