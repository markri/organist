<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * new structure
 *
 * Using formtypes as extra parameters, instead of key value storage in database always using Parameter object
 * This way we have full flexibility in assertion, default value assigning, form field specific settings, etc
 *
 * This is how it's going to work (taking Target as an example to assign extra params)
 *
 * in the target controller where the form is created for editing/creating the target
 * on non POST, just showing the form
 * call helper class which will get all tagged admin classes for type=target, return all forms
 * get the form and createview
 *
 * on creating/editing a target
 * bind all forms
 * validate all forms
 * persist Target
 * persist other forms
 *
 *
 * when retrieving parameters
 * get same helper class, setTarget
 * call getParameters to assign it to command
 *
 *
 * admin class
 *  - setTarget
 *  - getForm
 *  - getKeys
 *  - getParameters
 *  - bind
 *  - validate
 *  - persist
 *
 *
 *  * Visually
 *
 * on new application a user can select parameter sets (e.g. mysql params, jackrabbit params, solr params, etc, etc)
 *
 * when editing application all sets with its parameters will be fetched
 * extra sets can be connected (checkbox).
 *
 * Netvlies\Bundle\PublishBundle\Entity\ParameterSet
 *
 * @ORM\Entity(repositoryClass="Netvlies\Bundle\PublishBundle\Entity\ParameterSetRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"A" = "ParameterSetApplication", "E" = "Environment", "T" = "Target", "C" = "Command", "G" = "Global"})
 */
class ParameterSet
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
     * @var string $parameterSet
     *
     * @ORM\Column(name="parameterAdmin", type="string", length=255)
     */
    protected $parameterAdmin;

    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    protected $value;


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
     * Get message type
     *
     * @return string
     */
    public function getType()
    {
        return basename(get_class($this));
    }

    /**
     * @param string $parameterAdmin
     */
    public function setParameterAdmin($parameterAdmin)
    {
        $this->parameterAdmin = $parameterAdmin;
    }

    /**
     * @return string
     */
    public function getParameterKey()
    {
        return $this->parameterKey;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}