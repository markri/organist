<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 7/17/13
 * Time: 4:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Action;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;

class CheckoutCommand implements CommandApplicationInterface {


    /**
     * @var Application $application
     */
    protected $application;

    /**
     * @var VersioningInterface $versioningService
     */
    protected $versioningService;

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

    /**
     * @param VersioningInterface $versioningService
     */
    public function setVersioningService(VersioningInterface $versioningService)
    {
        $this->versioningService = $versioningService;
    }


    public function getCommand()
    {
        $appRoot = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        return sprintf('cd %s && app/console publish:checkout --key=%s', $appRoot, $this->getApplication()->getKeyName());
    }


    /**
     * Must return descriptive label for command type
     * @return string
     */
    public function getLabel()
    {
        return 'Local checkout';
    }


}