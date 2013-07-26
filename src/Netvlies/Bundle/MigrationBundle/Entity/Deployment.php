<?php

namespace Netvlies\Bundle\MigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deployment
 *
 * @ORM\Table(name="__Deployment")
 * @ORM\Entity
 */
class Deployment
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
     * @ORM\Column(name="currentRevision", type="string", length=255, nullable=true)
     */
    private $currentrevision;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="mysqldb", type="string", length=255, nullable=true)
     */
    private $mysqldb;

    /**
     * @var string
     *
     * @ORM\Column(name="mysqluser", type="string", length=255, nullable=true)
     */
    private $mysqluser;

    /**
     * @var string
     *
     * @ORM\Column(name="mysqlpw", type="string", length=255, nullable=true)
     */
    private $mysqlpw;

    /**
     * @var string
     *
     * @ORM\Column(name="currentBranch", type="string", length=255, nullable=true)
     */
    private $currentbranch;

    /**
     * @var boolean
     *
     * @ORM\Column(name="requiresRevision", type="boolean", nullable=true)
     */
    private $requiresrevision;

    /**
     * @var string
     *
     * @ORM\Column(name="approot", type="string", length=255, nullable=false)
     */
    private $approot;

    /**
     * @var string
     *
     * @ORM\Column(name="webroot", type="string", length=255, nullable=false)
     */
    private $webroot;

    /**
     * @var \Application
     *
     * @ORM\ManyToOne(targetEntity="Application")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     * })
     */
    private $application;

    /**
     * @var \Environment
     *
     * @ORM\ManyToOne(targetEntity="Environment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="environment_id", referencedColumnName="id")
     * })
     */
    private $environment;

    /**
     * @var \Phingtarget
     *
     * @ORM\ManyToOne(targetEntity="Phingtarget")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="phingTarget_id", referencedColumnName="id")
     * })
     */
    private $phingtarget;


}
