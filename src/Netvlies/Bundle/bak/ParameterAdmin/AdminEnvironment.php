<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/8/12
 * Time: 10:24 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishBundle\ParameterAdmin;
use Netvlies\Bundle\PublishBundle\Entity\Environment;

abstract class AdminEnvironment extends AdminDefault
{

    /**
     * You need to set the environment for this admin so the id of the environment entity can be stored with
     * the parameterset you implement
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Environment $environment
     */
    abstract public function setEnvironment(Environment $environment);


}
