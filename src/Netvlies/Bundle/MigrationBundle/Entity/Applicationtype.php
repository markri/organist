<?php

namespace Netvlies\Bundle\MigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Applicationtype
 *
 * @ORM\Table(name="ApplicationType")
 * @ORM\Entity
 */
class Applicationtype
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="deployCommand", type="string", length=255, nullable=true)
     */
    private $deploycommand;

    /**
     * @var string
     *
     * @ORM\Column(name="deployOCommand", type="string", length=255, nullable=true)
     */
    private $deployocommand;

    /**
     * @var string
     *
     * @ORM\Column(name="copyCommand", type="string", length=255, nullable=true)
     */
    private $copycommand;

    /**
     * @var string
     *
     * @ORM\Column(name="rollbackCommand", type="string", length=255, nullable=true)
     */
    private $rollbackcommand;

    /**
     * @var string
     *
     * @ORM\Column(name="setupTAPCommand", type="string", length=255, nullable=true)
     */
    private $setuptapcommand;

    /**
     * @return string
     */
    public function getCopycommand()
    {
        return $this->copycommand;
    }

    /**
     * @return string
     */
    public function getDeploycommand()
    {
        return $this->deploycommand;
    }

    /**
     * @return string
     */
    public function getDeployocommand()
    {
        return $this->deployocommand;
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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRollbackcommand()
    {
        return $this->rollbackcommand;
    }

    /**
     * @return string
     */
    public function getSetuptapcommand()
    {
        return $this->setuptapcommand;
    }


}
