<?php
/**
 * (c) Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Netvlies\Bundle\PublishBundle\Services;

use Netvlies\Bundle\PublishBundle\Entity\Application;

class ApplicationHelper
{

    protected $baseRepositoryPath;

    public function __construct($baseRepositoryPath)
    {
        $this->baseRepositoryPath = $baseRepositoryPath;
    }

    public function getRepositoryPath(Application $application)
    {
        return $this->baseRepositoryPath. DIRECTORY_SEPARATOR . $application->getKeyName();
    }

}
