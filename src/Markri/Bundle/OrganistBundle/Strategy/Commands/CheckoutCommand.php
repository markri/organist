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

namespace Markri\Bundle\OrganistBundle\Strategy\Commands;

use Markri\Bundle\OrganistBundle\Entity\Application;
use Markri\Bundle\OrganistBundle\Versioning\VersioningInterface;

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
     * @param \Markri\Bundle\OrganistBundle\Entity\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return \Markri\Bundle\OrganistBundle\Entity\Application
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
