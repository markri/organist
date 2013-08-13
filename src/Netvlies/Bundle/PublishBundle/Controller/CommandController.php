<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

use Netvlies\Bundle\PublishBundle\Action\CommandApplicationInterface;
use Netvlies\Bundle\PublishBundle\Action\CommandTargetInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;
use Netvlies\Bundle\PublishBundle\Action\CommandInterface;
use Netvlies\Bundle\PublishBundle\Action\DeployCommand;
use Netvlies\Bundle\PublishBundle\Action\RollbackCommand;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationDeployType;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationRollbackType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;




class CommandController extends Controller {


    /**
     * @Route("/application/{id}/commands")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @Template()
     * @param Application $application
     */
    public function commandPanelAction($application)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($application->getScmService());
        $repoPath = $versioningService->getRepositoryPath($application);

        if(!file_exists($repoPath)){
            return $this->redirect($this->generateUrl('netvlies_publish_application_checkoutrepository', array('id' => $application->getId())));
        }

        $deployCommand = new DeployCommand();
        $deployCommand->setApplication($application);
        $deployCommand->setVersioningService($versioningService);


        $rollbackCommand = new RollbackCommand();
        $rollbackCommand->setApplication($application);
        $rollbackCommand->setRepositoryPath($versioningService->getRepositoryPath($application));

        $deployForm = $this->createForm(new FormApplicationDeployType(), $deployCommand, array('app'=>$application));
        $rollbackForm = $this->createForm(new FormApplicationRollbackType(), $rollbackCommand, array('app'=>$application));

        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){

            if ($request->request->has($deployForm->getName())){

                $deployForm->bind($request);

                if($deployForm->isValid()){

                    return $this->forward('NetvliesPublishBundle:Command:execTargetCommand', array(
                        'command'  => $deployCommand
                    ));
                }
            }

            if ($request->request->has($rollbackForm->getName())){
                $rollbackForm->bind($request);

                if($rollbackForm->isValid()){

                    return $this->forward('NetvliesPublishBundle:Command:execTargetCommand', array(
                        'command'  => $rollbackCommand
                    ));
                }
            }
        }

        return array(
            'deployForm' => $deployForm->createView(),
            'rollbackForm' => $rollbackForm->createView(),
            'application' => $application,
        );
    }

    /**
     * Other controllers will forward to this action
     * @param CommandInterface $command
     * @template()
     */
    public function execTargetCommandAction(CommandTargetInterface $command)
    {
        $commandLog = new CommandLog();
        $versioningService = $this->container->get($command->getApplication()->getScmService());

        // Anyterm strips all env vars before executing exec.sh under user deploy
        // So we need to add it manually in order to find the appropiate keys for git repos and remote servers to deploy to
        $script = 'export HOME='.$_SERVER['HOME'].' && ';

        // Change dir to app repository
        $script .='cd '.$versioningService->getRepositoryPath($command->getApplication())." && ";
        $script .=$command->getCommand();

        $commandLog->setCommandLabel($command->getLabel());
        $commandLog->setCommand($script);
        $commandLog->setDatetimeStart(new \DateTime());
        $commandLog->setHost($command->getTarget()->getEnvironment()->getHostname());
        $commandLog->setTarget($command->getTarget());
        $commandLog->setType($command->getTarget()->getEnvironment()->getType());

        try{
            $userName = $this->get('security.context')->getToken()->getUser()->getUsername();
        }
        catch(\Exception $e){
            $userName = 'nobody';
        }

        $commandLog->setUser($userName);

        /**
         * @var EntityManager $em
         */
        $em  = $this->getDoctrine()->getManager();
        $em->persist($commandLog);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_command_exec', array('id' => $commandLog->getId())));
    }


    public function execApplicationCommandAction(CommandApplicationInterface $command)
    {
        $commandLog = new CommandLog();

        $script = '';
        $script .=$command->getCommand();

        $commandLog->setApplication($command->getApplication());
        $commandLog->setCommandLabel($command->getLabel());
        $commandLog->setCommand($script);
        $commandLog->setDatetimeStart(new \DateTime());
        $commandLog->setHost('localhost');

        try{
            $userName = $this->get('security.context')->getToken()->getUser()->getUsername();
        }
        catch(\Exception $e){
            $userName = 'nobody';
        }

        $commandLog->setUser($userName);

        /**
         * @var EntityManager $em
         */
        $em  = $this->getDoctrine()->getManager();
        $em->persist($commandLog);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_command_exec', array('id' => $commandLog->getId())));
    }


    /**
     * This route is fixed! Due to apache proxy setting that will redirect /console/exec/anyterm to appropriate assets
     * This action should never be called without having used the prepareCommand (which will prepare a log entry)
     *
     * @Route("/console/exec/{id}", requirements={"id" = "\d+"})
     * @ParamConverter("commandLog", class="NetvliesPublishBundle:CommandLog")
     * @Template()
     * @param CommandLog $commandLog
     */
    public function execAction($commandLog)
    {
        $application = $commandLog->getApplication();

        if($commandLog->getDatetimeEnd()){
            $this->get('session')->getFlashBag()->add('error', sprintf('This command is already executed. <a href="%s">Click here</a> if you want to re-execute it', $this->generateUrl('netvlies_publish_command_reexecute', array('id'=>$commandLog->getId()))));
            return $this->redirect($this->generateUrl('netvlies_publish_application_dashboard', array('id'=>$application->getId())));
        }

        return array(
            'command' => $commandLog,
            'application' => $application
        );
    }


    /**
     * @Route("/console/{application}/viewlog/{id}")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @ParamConverter("commandLog", class="NetvliesPublishBundle:CommandLog")
     * @Template()
     */
    public function viewLogAction($commandLog, $application)
    {
        return array(
            'log' => $commandLog,
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
            'logs' => $this->getDoctrine()->getRepository('NetvliesPublishBundle:CommandLog')->getLogsForApplication($application),
            'application' => $application
        );
    }


    /**
     * @Route("/console/reexecute/{id}")
     * @ParamConverter("commandLog", class="NetvliesPublishBundle:CommandLog")
     * @param CommandLog $commandLog
     */
    public function reExecuteAction($commandLog)
    {
        $newCommand = new CommandLog();
        $newCommand->setTarget($commandLog->getTarget());
        $newCommand->setType($commandLog->getType());
        $newCommand->setCommand($commandLog->getCommand());
        $newCommand->setDatetimeStart(new \DateTime());
        $newCommand->setHost($commandLog->getHost());
        $newCommand->setCommandLabel($commandLog->getCommandLabel());

        try{
            $userName = $this->get('security.context')->getToken()->getUser()->getUsername();
        }
        catch(\Exception $e){
            $userName = 'nobody';
        }

        $newCommand->setUser($userName);

        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $em->persist($newCommand);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_command_exec', array('id' => $newCommand->getId())));
    }



}