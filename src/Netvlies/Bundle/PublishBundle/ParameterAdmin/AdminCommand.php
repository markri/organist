<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/8/12
 * Time: 10:24 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishBundle\ParameterAdmin;

use Netvlies\Bundle\PublishBundle\Entity\Command;

abstract class AdminCommand extends AdminDefault
{

    /**
     * You need to set the command for this admin so the id of the command entity can be stored with
     * the parameterset you implement
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Command $command
     */
    abstract public function setApplication(Command $command);


}
