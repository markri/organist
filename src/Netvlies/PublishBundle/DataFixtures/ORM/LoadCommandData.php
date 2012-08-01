<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/1/12
 * Time: 8:57 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\PublishBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Netvlies\PublishBundle\Entity\Command;

class LoadCommandData implements FixtureInterface
{

    public function load(ObjectManager $om)
    {

        $commandsPerApptype = array(
            'symfony20' => array(
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
                array(
                    'displayname' => 'Open symfony 2.0 console',
                    'command' => ''
                )
            ),
            'symfony21' => array(
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
                array(
                    'displayname' => 'Open symfony 2.1 console',
                    'command' => ''
                )
            ),
            'custom' => array(
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
            )
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
