<?php
namespace Netvlies\PublishBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\PublishBundle\Entity\Environment;

class LoadEnvironmentData implements FixtureInterface
{

    public function load(ObjectManager $om)
    {

        $environment = new Environment();
        $environment->setKeyName('O.publish-o.nvsotap.nl');
        $environment->setType('O');
        $environment->setHostname('publish-o.nvsotap.nl');
        $environment->setSudoUser('deploy');
        $environment->setSshPort(22);
        $environment->setMysqlAdminUser('root');
        $environment->setMysqlAdminPassword('vagrant');
        $om->persist($environment);

        $environment = new Environment();
        $environment->setKeyName('T.publish-t.nvsotap.nl');
        $environment->setType('T');
        $environment->setHostname('publish-t.nvsotap.nl');
        $environment->setSudoUser('deploy');
        $environment->setSshPort(22);
        $environment->setMysqlAdminUser('root');
        $environment->setMysqlAdminPassword('vagrant');
        $om->persist($environment);


        $om->flush();
    }

    public function getOrder()
    {
        return 20;
    }

}
