<?php


namespace Netvlies\Bundle\PublishBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;

class LoadLog implements  FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $app = $manager->getRepository('NetvliesPublishBundle:Application')->findOneById(1);
        $target = $manager->getRepository('NetvliesPublishBundle:Target')->findOneById(1);


        $commandLog = new CommandLog();
        $commandLog->setType('P');
        $commandLog->setApplication($app);
        $commandLog->setCommand('ls -als');
        $commandLog->setCommandLabel('directory listing');
        $commandLog->setDatetimeStart(new \DateTime());
        $commandLog->setDatetimeEnd(new \DateTime());
        $commandLog->setExitCode(0);
        $commandLog->setHost('localhost');
        $commandLog->setLog('- no log due to test script - ');
        $commandLog->setTarget($target);
        $commandLog->setUser('phpunit');

        $manager->persist($commandLog);
        $manager->flush();
    }

}