<?php

namespace Netvlies\Bundle\MigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application
 *
 * @ORM\Table(name="Application")
 * @ORM\Entity
 */
class Application
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
     * @ORM\Column(name="repokey", type="string", length=255, nullable=true)
     */
    private $repokey;

    /**
     * @var string
     *
     * @ORM\Column(name="customer", type="string", length=255, nullable=false)
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="mysqlpw", type="string", length=255, nullable=true)
     */
    private $mysqlpw;

    /**
     * @var string
     *
     * @ORM\Column(name="gitrepoSSH", type="string", length=255, nullable=true)
     */
    private $gitrepossh;

    /**
     * @var string
     *
     * @ORM\Column(name="branchtofollow", type="string", length=255, nullable=true)
     */
    private $branchtofollow;

    /**
     * @var string
     *
     * @ORM\Column(name="referencetofollow", type="string", length=255, nullable=true)
     */
    private $referencetofollow;

    /**
     * @var \Applicationtype
     *
     * @ORM\ManyToOne(targetEntity="Applicationtype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @return string
     */
    public function getBranchtofollow()
    {
        return $this->branchtofollow;
    }

    /**
     * @return string
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return string
     */
    public function getGitrepossh()
    {
        return $this->gitrepossh;
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
    public function getMysqlpw()
    {
        return $this->mysqlpw;
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
    public function getReferencetofollow()
    {
        return $this->referencetofollow;
    }

    /**
     * @return string
     */
    public function getRepokey()
    {
        return $this->repokey;
    }

    /**
     * @return Applicationtype
     */
    public function getType()
    {
        return $this->type;
    }





}
