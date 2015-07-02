<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\Application;

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
        $app->setApplicationType('symfony23');
        $app->setCustomer('testcustomer');
        $app->setKeyName('testkey');
        $app->setName('testname');
        $app->setScmService('git');
        $app->setScmUrl('https://github.com/organist/puppet.git');
        $app->setDeploymentStrategy('Capistrano2');

        $manager->persist($app);
        $manager->flush();
    }

}