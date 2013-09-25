<?php


namespace Netvlies\Bundle\PublishBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\Environment;


class LoadEnvironment implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $env = new Environment();
        $env->setHostname('localhost');
        $env->setType('P');
        $env->setKeyName('P_localhost');

        $manager->persist($env);
        $manager->flush();
    }

}