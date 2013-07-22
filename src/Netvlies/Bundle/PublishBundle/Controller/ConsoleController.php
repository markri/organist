<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

use Doctrine\ORM\EntityManager;
use Netvlies\Bundle\PublishBundle\Action\CommandInterface;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\ConsoleLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


use Netvlies\Bundle\PublishBundle\Form\FormApplicationType;
use Symfony\Component\Validator\Constraints\DateTime;


class ConsoleController extends Controller {


    /**
     * Other controllers will forward to this action
     * @param CommandInterface $command
     * @template()
     */
    public function execCommandAction($command)
    {
        $logEntry = new ConsoleLog();
        $versioningService = $this->container->get($command->getApplication()->getScmService());

        $script = '';

        // Change dir to app repository
        $script .='cd '.$versioningService->getRepositoryPath($command->getApplication())." && \n";


        // Forward any keys connected to the SCM service
        /**
         * @var VersioningInterface $versioningService
         */

        if($versioningService->getPrivateKey()){
            // Forward optional keys for versioning
            $script .='eval `ssh-agent`'." && \n";
            $script .='`ssh-add '.$versioningService->getPrivateKey().'`'." && \n";
        }


        $script .=$command->getCommand()." && \n";

        if($versioningService->getPrivateKey()){
            // Kill forwarded keys
            $script .='ssh-agent -k > /dev/null 2>&1'." && \n";
            $script .='unset SSH_AGENT_PID'." && \n";
            $script .='unset SSH_AUTH_SOCK'." \n";
        }

        $logEntry->setCommand($script);
        $logEntry->setDatetimeStart(new \DateTime());
        $logEntry->setHost($command->getTarget()->getEnvironment()->getHostname());
        $logEntry->setTargetLabel($command->getTarget()->getLabel());
        $logEntry->setTargetId($command->getTarget()->getId());
        $logEntry->setType($command->getTarget()->getEnvironment()->getType());
        $logEntry->setRevision($command->getRevision());

        //@todo replace this with proper security auth token
        $logEntry->setUser(array_key_exists('PHP_AUTH_USER', $_SERVER)? $_SERVER['PHP_AUTH_USER'] : 'nobody');

        /**
         * @var EntityManager $em
         */
        $em  = $this->getDoctrine()->getManager();
        $em->persist($logEntry);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_console_exec', array('id' => $logEntry->getId())));
    }


    /**
     * This route is fixed! Due to apache proxy setting that will redirect /console/exec/anyterm to appropriate assets
     * This action should never be called without having used the prepareCommand (which will prepare a log entry)
     *
     * @Route("/console/exec/{id}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function execAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var ConsoleLog $log
         */
        $log = $em->getRepository('NetvliesPublishBundle:ConsoleLog')->find($id);
        if($log->getDatetimeEnd()){
            // @todo implement template with question to copy record and to reexecute it
            echo 'this action is already executed!';
            exit;
        }
        return array('id' => $id);
    }


    /**
     * @Route("/console/{application}/viewlog/{id}")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @ParamConverter("consoleLog", class="NetvliesPublishBundle:ConsoleLog")
     * @Template()
     */
    public function viewLogAction($consoleLog, $application)
    {
        return array(
            'log' => $consoleLog,
            'application' => $application
        );
    }


    /**
     * @Route("/console/logs/{id}")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @Template()
     * @param Application $application
     */
    public function listLogsAction($application)
    {
        return array(
            'logs' => $this->getDoctrine()->getRepository('NetvliesPublishBundle:ConsoleLog')->getLogsByTargets($application->getTargets()),
            'application' => $application
        );
    }


//    /**
//     * @todo this should be moved into DIC into a twig extension
//     * @Route("/console/frame/exec/{id}/{scriptpath}/{command}")
//     * @Template()
//     */
//    public function executeCommandAction($id, $command, $scriptpath){
//        // We must redirect in order to make use of the apache proxy setting which path is fixed in httpd.conf
//        // So therefore this method will just render a template where an iframe is loaded with an anyterm console where the command is executed
//        // The script (encoded scriptpath) will be selfdestructed at the end, so re-executing is impossible by then
//        $twigParams = array();
//        $em  = $this->getDoctrine()->getManager();
//        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);
//
//        $twigParams['application'] = $app;
//        $twigParams['command'] = $command;
//        $twigParams['scriptpath'] = $scriptpath;
//
//        return $twigParams;
//    }


}