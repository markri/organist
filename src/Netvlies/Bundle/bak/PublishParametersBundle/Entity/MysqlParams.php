<?php

namespace Netvlies\Bundle\PublishParametersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishParametersBundle\Entity\MysqlParams
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MysqlParams
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string $dbname
     *
     * @ORM\Column(name="dbname", type="string", length=255)
     */
    private $dbname;

    /**
     * @var string $hostname
     *
     * @ORM\Column(name="hostname", type="string", length=255)
     */
    private $hostname;

    /**
     * @var string $port
     *
     * @ORM\Column(name="port", type="string", length=255)
     */
    private $port;


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
     * Set username
     *
     * @param string $username
     * @return MysqlParams
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return MysqlParams
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set dbname
     *
     * @param string $dbname
     * @return MysqlParams
     */
    public function setDbname($dbname)
    {
        $this->dbname = $dbname;
        return $this;
    }

    /**
     * Get dbname
     *
     * @return string 
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * Set hostname
     *
     * @param string $hostname
     * @return MysqlParams
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
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
     * Set port
     *
     * @param string $port
     * @return MysqlParams
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Get port
     *
     * @return string 
     */
    public function getPort()
    {
        return $this->port;
    }
}