<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 13-1-12
 * Time: 9:52
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;


class GetCommandCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('publish:getcommand')
            ->setDescription('Gets command that is connected to id.')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'script id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getOption('id');


        /**
         * @var EntityManager $em
         */
        $em = $this->getContainer()->get('doctrine')->getManager();
        /**
         * @var CommandLog $commandLog
         */
        $commandLog = $em->getRepository('NetvliesPublishBundle:CommandLog')->find($id);

        $output->write($commandLog->getCommand());

   }
}