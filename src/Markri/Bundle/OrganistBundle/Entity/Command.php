<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 7/3/15
 * Time: 10:23 PM
 */

namespace Markri\Bundle\OrganistBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Command
 * @package Markri\Bundle\OrganistBundle\Entity
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
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var Application $target
     * @ORM\ManyToOne(targetEntity="Application", inversedBy="commands")
     */
    private $application;

    /**
     * @var string
     * @ORM\Column(name="template", type="text")
     */
    private $template;

    /**
     * @ORM\Column(name="form_config", type="text")
     */
    private $formConfig;

    /**
     * @param \Markri\Bundle\OrganistBundle\Entity\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return \Markri\Bundle\OrganistBundle\Entity\Application
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

    /**
     * @param mixed $formConfig
     */
    public function setFormConfig($formConfig)
    {
        $this->formConfig = $formConfig;
    }

    /**
     * @return mixed
     */
    public function getFormConfig()
    {
        return $this->formConfig;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }
} 