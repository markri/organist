<?php
namespace Netvlies\Bundle\PublishBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\ParameterApplication;

class LoadApplicationData implements OrderedFixtureInterface, FixtureInterface
{

    public function load(ObjectManager $om)
    {
        $om->clear();
        $application = new Application();
        $application->setKeyName('test_symfony21');
        $application->setName('Test symfony 2.1');
        $application->setType($om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName('symfony21'));
        $application->setCustomer('Netvlies');
        $application->setScmService('git');
        $om->persist($application);


        $application = new Application();
        $application->setKeyName('test_symfony20');
        $application->setName('Test symfony 2.0');
        $application->setType($om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName('symfony20'));
        $application->setCustomer('Netvlies');
        $application->setScmService('git');
        $om->persist($application);

        $application = new Application();
        $application->setKeyName('test_custom');
        $application->setName('Test custom');
        $application->setType($om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName('custom'));
        $application->setCustomer('Netvlies');
        $application->setScmService('git');
        $om->persist($application);

        $om->flush();
    }

    public function getOrder()
    {
        return 50;
    }

}
