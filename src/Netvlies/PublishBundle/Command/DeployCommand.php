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
use Netvlies\PublishBundle\Entity\Deployment;
use Netvlies\PublishBundle\Controller\ConsoleController;

class DeployCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // We need to make an alternative interpretation of anyterm.js to connect to internal hosted anyterm
        // JS wont run on command line :-)
        // or we could use tail -f or something like that in combination with curl

        $this
            ->setName('publish:deploy')
            ->setDescription('Make a deployment through command line')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Target id')
            ->addOption('reference', null, InputOption::VALUE_OPTIONAL, 'Reference/revision', 'refs/heads/master');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getOption('id');
        $reference = $input->getOption('reference');

        if(empty($id)){
            throw new \Exception('Target id is required');
            return;
        }

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        /**
         * @var \Netvlies\PublishBundle\Entity\Target $target
         */
        $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneByid($id);

        $deployment = new Deployment();
        $deployment->setReference($reference);
        $deployment->setTarget($target);
        $console = $this->getContainer()->get('console_controller');
        $execParams = $console->deployAction($deployment);
        $scriptPath = $execParams['scriptpath'];

        $script = base64_decode($scriptPath);
        $thisDir = dirname(__FILE__);
        $cmd = $thisDir.'/exec.sh '.$script;

        $rShell = popen($cmd, 'r');
        $sLineBuffer = '';

        while (!feof($rShell)) {

            $sChar = fread($rShell, 1);

            if (ord($sChar) == 10) {
                // line ending
                echo $sLineBuffer."\n";
                $sLineBuffer = '';

            } else {
                $sLineBuffer.=$sChar;
            }
        }

        echo $sLineBuffer;
        exit(pclose($rShell));
    }
}