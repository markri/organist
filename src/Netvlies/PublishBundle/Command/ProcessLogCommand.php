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
         $logDir = dirname(dirname(dirname(dirname(__DIR__)))).'/app/logs/scripts';

         $uids = array();

         // Also include log files older than 2 weeks. They're probably not going to finish anyway. So clear them
         $iterator = new DirectoryIterator($logDir);
         foreach ($iterator as $fileinfo) {
             if ($fileinfo->isFile()) {
                 if(time() - $fileinfo->getMTime() > 60 * 60 * 24 * 14){
                     $uids[] = $fileinfo->getBasename();
                 }
             }
         }


         if(!empty($uid)){
             $uids[] = $uid;
         }

         $em = $this->getContainer()->get('doctrine')->getEntityManager();


         foreach($uids as $uid){
             /**
              * @var \Netvlies\PublishBundle\Entity\DeploymentLog $logentry
              */
             $logentry = $em->getRepository('NetvliesPublishBundle:DeploymentLog')->findOneByUid($uid);

             if(is_null($logentry)){
                 echo "Warning: No log entry available to update...";
                 continue;
             }


             $logfile = $logDir.'/'.$uid.'.log';
             $logentry->setDatetimeEnd(new \DateTime());
             $logentry->setLog(file_get_contents($logfile));
             $logentry->setExitCode($exitcode);

             $em->persist($logentry);
             $em->flush();
             unlink($logfile);

             $targetId = $logentry->getTargetId();
             if(empty($targetId)){
                 echo "Notice: No connected target to update ...";
                 continue;
             }

             /**
              * @var \Netvlies\PublishBundle\Entity\Target $target
              */
             $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($logentry->getTargetId());

             $command = 'ssh '.$target->getUsername().'@'.$target->getEnvironment()->getHostname().' cat '.$target->getApproot().'/REVISION';
             $revision = shell_exec($command);
             $target->setCurrentRevision($revision);
             $em->persist($target);
             $em->flush();
         }
     }
}