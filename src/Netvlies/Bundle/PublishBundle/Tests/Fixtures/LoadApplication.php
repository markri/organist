<?php


namespace Netvlies\Bundle\PublishBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\Application;

class LoadApplication implements  FixtureInterface
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

        $manager->persist($app);
        $manager->flush();
    }

}