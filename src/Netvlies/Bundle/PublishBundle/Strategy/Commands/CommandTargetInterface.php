<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Strategy\Commands;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Target;

interface CommandTargetInterface
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
     * Target is required, so must return instance of entity Target
     * @return Target
     */
    public function getTarget();


    /**
     * Optional revision that is to be used
     * @return string
     */
    public function getRevision();

    /**
     * Required
     * @return string
     */
    public function getRepositoryPath();


    /**
     * Must return descriptive label for command type
     * @return string
     */
    public function getLabel();


}
