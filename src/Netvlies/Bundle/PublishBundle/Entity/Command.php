<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 7/3/15
 * Time: 10:23 PM
 */

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Command
 * @package Netvlies\Bundle\PublishBundle\Entity
 * @ORM\Entity()
 * @ORM\Table()
 */
class Command
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
     * @var Application $target
     * @ORM\ManyToOne(targetEntity="Application")
     */
    private $application;

    /**
     * @var string
     * @ORM\Column(name="template", type="text")
     */
    private $template;

    /**
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return \Netvlies\Bundle\PublishBundle\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
} 