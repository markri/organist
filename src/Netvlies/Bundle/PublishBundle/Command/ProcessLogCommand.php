<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 13-1-12
 * Time: 9:52
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Netvlies\Bundle\PublishBundle\Entity\ConsoleLog;


class ProcessLogCommand extends ContainerAwareCommand
{

     protected function configure()
     {
         $this
             ->setName('publish:processlog')
             ->setDescription('Processes log entry of given command.')
             ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Execution UID')
             ->addOption('exitcode', null, InputOption::VALUE_OPTIONAL, 'Exit code')
         ;
     }

     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $id = $input->getOption('id');
         $exitcode = $input->getOption('exitcode');
         $logDir = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/app/logs/scripts';

         $ids = array();

         // Also include log files older than 24 hours. They're probably not going to finish anyway. So clear them
         $iterator = new \DirectoryIterator($logDir);
         foreach ($iterator as $fileinfo) {
             if ($fileinfo->isFile()) {
                 if(time() - $fileinfo->getMTime() > 60 * 60 * 24 * 1){
                     $ids[] = $fileinfo->getBasename();
                 }
             }
         }

         if(!empty($id)){
             $ids[] = $id;
         }

         $em = $this->getContainer()->get('doctrine')->getManager();


         foreach($ids as $id){
             /**
              * @var \Netvlies\Bundle\PublishBundle\Entity\ConsoleLog $logentry
              */
             $logentry = $em->getRepository('NetvliesPublishBundle:ConsoleLog')->find($id);

             if(is_null($logentry)){
                 echo "Warning: No log entry available to update, remove log manually...";
                 continue;
             }

             $logfile = $logDir.'/'.$id.'.log';
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
              * @var \Netvlies\Bundle\PublishBundle\Entity\Target $target
              */
             $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($logentry->getTargetId());

             $command = 'ssh '.$target->getUsername().'@'.$target->getEnvironment()->getHostname().' cat '.$target->getApproot().'/REVISION || true';
             $revision = shell_exec($command);
             $target->setLastDeployedRevision($revision);
             //@todo set last deployed branch/tag as well


             $em->persist($target);
             $em->flush();
         }
     }
}