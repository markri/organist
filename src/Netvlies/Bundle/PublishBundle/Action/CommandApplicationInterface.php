<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Action;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Target;

interface CommandApplicationInterface
{

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