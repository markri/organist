<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/8/12
 * Time: 10:24 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishBundle\ParameterAdmin;
use Netvlies\Bundle\PublishBundle\Entity\Application;

abstract class AdminApplication extends AdminDefault
{

    /**
     * You need to set the application for this admin so the id of the application entity can be stored with
     * the parameterset you implement
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $application
     */
    abstract public function setApplication(Application $application);


}
