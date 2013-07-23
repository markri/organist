<?php
namespace Netvlies\Bundle\PublishOMSBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\ApplicationType;

class LoadApplicationTypeData implements FixtureInterface
{

    public function load(ObjectManager $om)
    {

        $types = array(
            array(
                'keyName' => 'oms',
                'displayName' => 'OMS',
                'initScript' => dirname(dirname(__DIR__)).'/Resources/apptypes/OMS/init.sh'
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
