<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 13-1-12
 * Time: 9:52
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\PublishBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Netvlies\PublishBundle\Entity\DeploymentLog;


class ProcessLogCommand extends ContainerAwareCommand
{

     protected function configure()
     {
         $this
             ->setName('publish:processlog')
             ->setDescription('Processes log entry of given command.')
             ->addOption('uid', null, InputOption::VALUE_OPTIONAL, 'Execution UID')
             ->addOption('exitcode', null, InputOption::VALUE_OPTIONAL, 'Exit code')
         ;
     }

     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $uid = $input->getOption('uid');
         $exitcode = $input->getOption('exitcode');

         if(empty($uid)){
             //@todo delete logs/scripts older than 2 days and update them in database
             return;
         }

         $em = $this->getContainer()->get('doctrine')->getEntityManager();
         /**
          * @var \Netvlies\PublishBundle\Entity\DeploymentLog $logentry
          */
         $logentry = $em->getRepository('NetvliesPublishBundle:DeploymentLog')->findOneByUid($uid);

         $logfile = dirname(dirname(dirname(dirname(__DIR__)))).'/app/logs/scripts/'.$uid.'.log';
         $logentry->setDatetimeEnd(new \DateTime());
         $logentry->setLog(file_get_contents($logfile));
         $logentry->setExitCode($exitcode);

         $em->persist($logentry);
         $em->flush();
         unlink($logfile);

         // Update target with revision if applicable
         $targetId = $logentry->getDeploymentId();
         $revision = $logentry->getRevision();

         if(!empty($targetId) && !empty($revision)){
             /**
              * @var \Netvlies\PublishBundle\Entity\Deployment $deployment
              */
             $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($targetId);
             $target->setCurrentRevision($revision);
             $em->persist($target);
             $em->flush();
         }
     }
}