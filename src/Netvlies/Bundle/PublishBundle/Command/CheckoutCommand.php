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

use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Netvlies\Bundle\PublishBundle\Entity\Application;

class CheckoutCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('publish:checkout')
            ->setDescription('Used to checkout repository if it is not already checked out')
            ->addOption('key', null, InputOption::VALUE_REQUIRED, 'key')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getOption('key');

        /**
         * @var EntityManager $em
         */
        $em = $this->getContainer()->get('doctrine')->getManager();

        /**
         * @var Application $application
         */
        $application = $em->getRepository('NetvliesPublishBundle:Application')->findOneByKeyName($key);
        if(empty($application)){
            $output->writeln(sprintf('Application with %s doesnt exist', $key));
            return;
        }

        /**
         * @var VersioningInterface $versioning
         */
        $versioning = $this->getContainer()->get($application->getScmService());
        $output->writeln(sprintf('Checking out application %s, this might take a while depending on repository size ...', $application->getName()));
        $versioning->checkoutRepository($application);
    }
}