<?php

namespace Netvlies\Bundle\PublishBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Environment;
use Netvlies\Bundle\PublishBundle\Entity\Target;

class LoadDemoData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $app = new Application();
        $app->setApplicationType('netvlies_publish.type.symfony23');
        $app->setCustomer('testcustomer');
        $app->setKeyName('testkey');
        $app->setName('testname');
        $app->setScmService('netvlies_publish.versioning.git');
        $app->setScmUrl('https://github.com/organist/puppet.git');
        $app->setDeploymentStrategy('Capistrano2');

        $manager->persist($app);
        $manager->flush();

        $env = new Environment();
        $env->setHostname('localhost');
        $env->setType('P');
        $env->setPort(22);

        $manager->persist($env);
        $manager->flush();

        $env = $manager->getRepository('NetvliesPublishBundle:Environment')->findOneById(1);
        $app = $manager->getRepository('NetvliesPublishBundle:Application')->findOneById(1);

        $target = new Target();
        $target->setEnvironment($env);
        $target->setApplication($app);
        $target->setUsername('vagrant');
        $target->setApproot('/home/vagrant/test');
        $target->setWebroot('/home/vagrant/test');
        $target->setCaproot('/home/vagrant/test');
        $target->setLabel('testtarget');
        $manager->persist($target);
        $manager->flush();
    }


}