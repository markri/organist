<?php

namespace Netvlies\Bundle\PublishParametersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishParametersBundle\Entity\JackrabbitParams
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class JackrabbitParams
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
     * @var string $workspace
     *
     * @ORM\Column(name="workspace", type="string", length=255)
     */
    private $workspace;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set workspace
     *
     * @param string $workspace
     * @return jackrabbitParams
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
        return $this;
    }

    /**
     * Get workspace
     *
     * @return string 
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return jackrabbitParams
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
     * @return jackrabbitParams
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
}