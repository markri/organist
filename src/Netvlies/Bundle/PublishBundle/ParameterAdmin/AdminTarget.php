<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/8/12
 * Time: 10:24 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishBundle\ParameterAdmin;
use Netvlies\Bundle\PublishBundle\Entity\Target;

abstract class AdminTarget extends AdminDefault
{

    protected $target;

    /**
     * You need to set the target for this admin so the id of the target entity can be stored with
     * the parameterset you implement
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Target $target
     */
    public function setTarget(Target $target)
    {
        $this->target = $target;
    }


}
