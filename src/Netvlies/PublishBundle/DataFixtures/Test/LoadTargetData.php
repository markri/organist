<?php
namespace Netvlies\PublishBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\PublishBundle\Entity\Target;
use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\Environment;

class LoadTargetData implements FixtureInterface
{

    public function load(ObjectManager $om)
    {
        $apps = $om->getRepository('NetvliesPublishBundle:Application')->findAll();
        $envs = $om->getRepository('NetvliesPublishBundle:Environment')->findAll();

        //@todo cleanup iteration, since this is a bunch of exceptions anyway, keep it simple by just creating a target for every app on every env that is needed

        foreach($apps as $app){
            /**
             * @var Application $app
             */
            foreach($envs as $env){
                /**
                 * @var Environment $env
                 */
                $target = new Target();
                $target->setApplication($app);
                $target->setEnvironment($env);

                if($env->getType()=='T'){
                    $appRoot = '/home/'.$app->getScmKey().'/www/current';
                    $capRoot = '/home/'.$app->getScmKey().'/www';
                    $webRoot = '/home/'.$app->getScmKey().'/www/current/web';

                }
                else{
                    $appRoot = '/home/vagrant/vhosts/'.$app->getScmKey();
                    $capRoot = '';
                    $webRoot = '/home/vagrant/vhosts/'.$app->getScmKey().'/web';
                }


                $target->setApproot($appRoot);
                $target->setCaproot($capRoot);
                $target->setWebroot($webRoot);

                $target->setLabel('('.$env->getType().') '.$env->getHostname());



            }
        }

        $om->flush();
    }

    public function getOrder()
    {
        return 30;
    }

}
