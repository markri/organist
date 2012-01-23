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
use Netvlies\PublishBundle\Controller\ConsoleController;

class getSettingsCommand extends ContainerAwareCommand
{

     protected function configure()
     {
         // @todo add label option. This needs to be implemented in entity first
         $this
             ->setName('publish:getsettings')
             ->setDescription('Display settings needed for deployment. Needs deploymentid')
             ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Deployment id')
             ->addOption('branch', null, InputOption::VALUE_OPTIONAL, 'Deployment id', 'refs/heads/master')
         ;
     }

     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $id = $input->getOption('id');
         $branch = $input->getOption('branch');

         if(empty($id)){
             return;
         }

         $em = $this->getContainer()->get('doctrine')->getEntityManager();
         /**
          * @var \Netvlies\PublishBundle\Entity\Deployment $deployment
          */
         $deployment = $em->getRepository('NetvliesPublishBundle:Deployment')->findOneByid($id);

         $app = $deployment->getApplication();
         $app->setBaseRepositoriesPath($this->getContainer()->getParameter('repositorypath'));
         $branches = $app->getRemoteBranches();

         if(!in_array($branch, $branches)){
            throw new \Exception('No such branch '.$branch);
         }

         $reference = array_search($branch, $branches);

         $console = new  ConsoleController();
         $params = $console->getSettings($this->getContainer(), $deployment, $reference);

        foreach($params as $param){
            echo substr($param, 2)."\n";
        }

     }
}