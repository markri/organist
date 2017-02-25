<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Markri\Bundle\OrganistBundle\Entity\Application;

class LoadApplication implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $app = new Application();
        $app->setApplicationType('organist.type.symfony');
        $app->setCustomer('testcustomer');
        $app->setKeyName('testkey');
        $app->setName('testname');
        $app->setScmService('organist.versioning.git');
        $app->setScmUrl('https://github.com/organist/puppet.git');
        $app->setDeploymentStrategy('capistrano2');

        $manager->persist($app);
        $manager->flush();
    }

}
