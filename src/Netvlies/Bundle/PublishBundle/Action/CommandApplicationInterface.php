<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 7/17/13
 * Time: 4:50 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Action;


use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Target;

interface CommandApplicationInterface {

    /**
     * Must return the entire command as string
     *
     * @return string
     */
    public function getCommand();


    /**
     * Application is required so must return instance of entity Application
     * @return Application
     */
    public function getApplication();


    /**
     * Must return descriptive label for command type
     * @return string
     */
    public function getLabel();


}