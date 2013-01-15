<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/1/12
 * Time: 8:57 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishOMSBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\Bundle\PublishBundle\Entity\Command;

class LoadCommandData implements FixtureInterface
{

    public function load(ObjectManager $om)
    {

        $commandsPerApptype = array(
            'oms' => array(
                array(
                    'displayname' => 'Deploy',
                    'command' => ''
                ),
                array(
                    'displayname' => 'Rollback',
                    'command' => ''
                ),
                array(
                    'displayname' => 'Copy content',
                    'command' => ''
                ),
                array(
                    'displayname' => 'Copy database',
                    'command' => ''
                ),
            ),
        );

        foreach ($commandsPerApptype as $appType => $commands) {
            $appType = $om->getRepository('NetvliesPublishBundle:ApplicationType')->findOneByKeyName($appType);

            foreach($commands as $commandInfo){
                $command = new Command();
                $command->setApplicationType($appType);
                $command->setDisplayName($commandInfo['displayname']);
                $command->setCommand($commandInfo['command']);

                $om->persist($command);
            }
        }

        $om->flush();
    }


    public function getOrder()
    {
        return 20;
    }


}
