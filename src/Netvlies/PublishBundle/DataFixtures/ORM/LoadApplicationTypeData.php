<?php
namespace Netvlies\PublishBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\PublishBundle\Entity\ApplicationType;

class LoadApplicationTypeData implements FixtureInterface
{

    public function load(ObjectManager $om)
    {

        $types = array(
            array(
                'keyName' => 'symfony20',
                'displayName' => 'Symfony 2.0',
                'initScript' => dirname(dirname(__DIR__)).'/Resources/apptypes/symfony20/init.sh'
            ),
            array(
                'keyName' => 'symfony21',
                'displayName' => 'Symfony 2.1',
                'initScript' => dirname(dirname(__DIR__)).'/Resources/apptypes/symfony21/init.sh'
            ),
            array(
                'keyName' => 'custom',
                'displayName' => 'Custom type',
                'initScript' => dirname(dirname(__DIR__)).'/Resources/apptypes/custom/init.sh'
            ),
        );

        foreach($types as $type){
            $appType = new ApplicationType();
            $appType->setKeyName($type['keyName']);
            $appType->setDisplayName($type['displayName']);
            $appType->setInitScript($type['initScript']);

            $om->persist($appType);
        }

        $om->flush();

    }

    public function getOrder()
    {
        return 10;
    }

}
