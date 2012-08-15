<?php
namespace Netvlies\Bundle\PublishBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\ParameterApplication;

class LoadApplicationData implements FixtureInterface
{

    public function load(ObjectManager $om)
    {
        $application = new Application();
        $application->setKeyName('publishtest_symfony21');
        $application->setName('Test symfony 2.1');
        $application->setType($om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName('symfony21'));
        $application->setCustomer('Netvlies');
        $application->setScmService('git');
        $om->persist($application);


        $application = new Application();
        $application->setKeyName('publishtest_symfony20');
        $application->setName('Test symfony 2.0');
        $application->setType($om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName('symfony20'));
        $application->setCustomer('Netvlies');
        $application->setScmService('git');
        $om->persist($application);

        $application = new Application();
        $application->setKeyName('publishtest_custom');
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
