<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 *
 */

namespace Netvlies\Bundle\PublishBundle\Action;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;

class CheckoutCommand implements CommandApplicationInterface
{
    /**
     * @var Application $application
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

    /**
     * @return string
     */
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