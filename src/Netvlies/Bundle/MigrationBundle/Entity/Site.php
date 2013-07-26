<?php

namespace Netvlies\Bundle\MigrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="__Site")
 * @ORM\Entity
 */
class Site
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
     * @ORM\Column(name="repository", type="string", length=255, nullable=false)
     */
    private $repository;

    /**
     * @var string
     *
     * @ORM\Column(name="buildfile", type="string", length=255, nullable=false)
     */
    private $buildfile;


}
