<?php
namespace Netvlies\Bundle\MigrationBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Environment
 *
 * @ORM\Table(name="Environment")
 * @ORM\Entity
 */
class Environment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="hostname", type="string", length=255, nullable=false)
     */
    private $hostname;

    /**
     * @var string
     *
     * @ORM\Column(name="sudoUser", type="string", length=255, nullable=false)
     */
    private $sudouser;

    /**
     * @var string
     *
     * @ORM\Column(name="homedirsBase", type="string", length=255, nullable=true)
     */
    private $homedirsbase;

    /**
     * @var string
     *
     * @ORM\Column(name="keyname", type="string", length=255, nullable=false)
     */
    private $keyname;

    /**
     * @var string
     *
     * @ORM\Column(name="deploybridgecommand", type="string", length=255, nullable=false)
     */
    private $deploybridgecommand;

    /**
     * @var string
     *
     * @ORM\Column(name="defaultuser", type="string", length=255, nullable=false)
     */
    private $defaultuser;

    /**
     * @return string
     */
    public function getDefaultuser()
    {
        return $this->defaultuser;
    }

    /**
     * @return string
     */
    public function getDeploybridgecommand()
    {
        return $this->deploybridgecommand;
    }

    /**
     * @return string
     */
    public function getHomedirsbase()
    {
        return $this->homedirsbase;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKeyname()
    {
        return $this->keyname;
    }

    /**
     * @return string
     */
    public function getSudouser()
    {
        return $this->sudouser;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }



}
