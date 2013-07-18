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

interface CommandInterface {

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
     * Setter for repository path.
     * @return mixed
     */
    public function setRepositoryPath($repositoryPath);

}