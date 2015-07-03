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

namespace Netvlies\Bundle\PublishBundle\Strategy\Commands;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;

class CheckoutCommand implements CommandApplicationInterface
{
    /**
     * @var Application $application
     */
    protected $application;


    /**
     * @var string $environment
     */
    protected $environment;


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
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        $appRoot = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        return sprintf('cd %s && app/console organist:checkout --key="%s" --env=%s', $appRoot, $this->getApplication()->getKeyName(), $this->environment);
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
