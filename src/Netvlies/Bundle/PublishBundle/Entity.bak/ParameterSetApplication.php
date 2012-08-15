<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\Bundle\PublishBundle\Entity\ParameterSetApplication
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ParameterSetApplication extends ParameterSet
{

    /**
     * @var Application $application
     *
     * @ORM\ManyToOne(targetEntity="Application")
     */
    protected $application;


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
}