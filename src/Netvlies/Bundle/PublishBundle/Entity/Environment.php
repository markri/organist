<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Netvlies\Bundle\PublishBundle\Entity\Environment
 *
 * @ORM\Entity(repositoryClass="Netvlies\Bundle\PublishBundle\Entity\EnvironmentRepository")
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
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank(message="type is required")
     * @Assert\Choice(choices = {"D", "T", "A", "P"}, message="Choose a valid servertype: D, T, A or P")
     */
    protected $type;

    /**
     * @ORM\Column(name="hostname", type="string", length=255)
     * @Assert\NotBlank(message="hostname is required")
     */
    protected $hostname;

    /**
     * @var object $targets
     * @ORM\OneToMany(targetEntity="Target", mappedBy="environment")
     */
    protected $targets;

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
     * @return object
     */
    public function getTargets()
    {
        return $this->targets;
    }


    /**
     * To identify this entity in forms
     */
    public function __toString()
    {
        return $this->getType().' ('.$this->getHostname().')';
    }
}