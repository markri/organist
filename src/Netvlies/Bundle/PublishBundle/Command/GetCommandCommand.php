<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
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