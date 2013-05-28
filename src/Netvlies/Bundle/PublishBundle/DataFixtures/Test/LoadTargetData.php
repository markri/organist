<?php
namespace Netvlies\Bundle\PublishBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Environment;

class LoadTargetData implements OrderedFixtureInterface, FixtureInterface
{

    public function load(ObjectManager $om)
    {
        $appRepo = $om->getRepository('NetvliesPublishBundle:Application');
        $envRepo = $om->getRepository('NetvliesPublishBundle:Environment');

        $target = new Target();
        $target->setApplication($appRepo->findOneByKeyName('publishtest_symfony21'));
        $target->setEnvironment($envRepo->findOneByKeyName('T.publish-t.nvsotap.nl'));
        $target->setLabel('(T) symfony21.publish-t.nvsotap.nl');
        $target->setCaproot('/home/publishtest_symfony21/www');
        $target->setApproot('/home/publishtest_symfony21/www/current');
        $target->setWebroot('/home/publishtest_symfony21/www/current/web');
        $om->persist($target);



        $target = new Target();
        $target->setApplication($appRepo->findOneByKeyName('publishtest_symfony21'));
        $target->setEnvironment($envRepo->findOneByKeyName('O.publish-o.nvsotap.nl'));
        $target->setLabel('(O) symfony21.publish-o.nvsotap.nl');
        $target->setCaproot('');
        $target->setApproot('/home/vagrant/vhosts/symfony21');
        $target->setWebroot('/home/vagrant/vhosts/symfony21/web');
        $om->persist($target);

        $om->flush();
    }

    public function getOrder()
    {
        return 70;
    }

}
