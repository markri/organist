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
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishBundle\Versioning\ReferenceInterface;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateVersionCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('organist:updateversion')
            ->setDescription('Get currently deployed version of given target.')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Target id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getOption('id');

        /**
         * @var EntityManager $em
         */
        $em = $this->getContainer()->get('doctrine')->getManager();

        /**
         * @var Target $target
         */
        $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($id);

        if (!$target) {
            $output->writeln(sprintf('No target found with id %s', $id));
            return;
        }


        //@todo retrieval of revision can be tricky, because of different SSH port, we should make port an option in environment
        $command = 'ssh ' . $target->getUsername() . '@' . $target->getEnvironment()->getHostname() . ' cat ' . $target->getApproot() . '/REVISION || true';
        $revision = trim(shell_exec($command));

        $output->writeln(sprintf('Found revision %s on target %s', $revision, $target->getLabel()));

        if (!empty($revision)) {
            $target->setLastDeployedRevision($revision);

            /**
             * @var VersioningInterface $versioningService
             */
            $versioningService = $this->getContainer()->get($target->getApplication()->getScmService());

            // Find tag
            $tags = $versioningService->getTags($target->getApplication());

            foreach ($tags as $reference) {
                /**
                 * @var ReferenceInterface $reference
                 */
                if ($reference->getReference() != $revision) {
                    continue;
                }

                $target->setLastDeployedTag($reference->getName());
                break;
            }

            // Find branch
            $branches = $versioningService->getBranches($target->getApplication());
            foreach ($branches as $reference) {
                /**
                 * @var ReferenceInterface $reference
                 */
                if ($reference->getReference() != $revision) {
                    continue;
                }

                $target->setLastDeployedBranch($reference->getName());
                break;
            }

            $em->persist($target);
            $em->flush();
        }
    }
}