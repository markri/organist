<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishBundle\Entity\Domain
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Netvlies\Bundle\PublishBundle\Entity\Domain")
 */
class Domain
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Target")
     */
    protected $target;

    /**
     * @var string $transport
     * @todo assert http|https
     * @ORM\Column(name="transport", type="string", length=255)
     */
    protected $transport;

    /**
     * @var string $domain
     * @ORM\Column(name="domain", type="string", length=255)
     */
    protected $domain;


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
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param $target Target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return Target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $transport
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return string
     */
    public function getTransport()
    {
        return $this->transport;
    }
}