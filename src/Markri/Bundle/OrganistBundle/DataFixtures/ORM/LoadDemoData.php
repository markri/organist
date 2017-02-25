<?php

namespace Markri\Bundle\OrganistBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Markri\Bundle\OrganistBundle\Entity\Application;
use Markri\Bundle\OrganistBundle\Entity\Environment;
use Markri\Bundle\OrganistBundle\Entity\Target;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class LoadDemoData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $app = new Application();
        $app->setApplicationType('organist.type.symfony23');
        $app->setCustomer('testcustomer');
        $app->setKeyName('testkey');
        $app->setName('testname');
        $app->setScmService('organist.versioning.git');
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

        $env = $manager->getRepository('OrganistBundle:Environment')->findOneById(1);
        $app = $manager->getRepository('OrganistBundle:Application')->findOneById(1);

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


        $fs = new Filesystem();
        $gitrepo = '/var/www/html/organist/web/repos/testkey';
        $fs->mkdir($gitrepo);

        $process = new Process(sprintf('cd %s && git init', $gitrepo));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }


        echo $process->getOutput();
    }


}