<?php
namespace Netvlies\PublishBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\PublishBundle\Entity\Application;

class LoadApplicationData implements FixtureInterface
{

    public function load(ObjectManager $om)
    {
        $application = new Application();
        $application->setCustomer('Netvlies');
        $application->setDefaultMysqlPass('vagrant');
        $application->setName('Test symfony 2.1');
        $application->setScmKey('publishtest_symfony21');
        $application->setScmService('git');
        $application->setType($om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName('symfony21'));
        $om->persist($application);

        $application = new Application();
        $application->setCustomer('Netvlies');
        $application->setDefaultMysqlPass('vagrant');
        $application->setName('Test symfony 2.0');
        $application->setScmKey('publishtest_symfony20');
        $application->setScmService('git');
        $application->setType($om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName('symfony20'));
        $om->persist($application);

        $application = new Application();
        $application->setCustomer('Netvlies');
        $application->setDefaultMysqlPass('vagrant');
        $application->setName('Test custom');
        $application->setScmKey('publishtest_custom');
        $application->setScmService('git');
        $application->setType($om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName('custom'));
        $om->persist($application);

        $om->flush();
    }

    public function getOrder()
    {
        return 10;
    }

}
