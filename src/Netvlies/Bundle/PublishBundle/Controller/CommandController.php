<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Controller;

use Netvlies\Bundle\PublishBundle\Entity\Command;
use Netvlies\Bundle\PublishBundle\Entity\CommandTemplate;
use Netvlies\Bundle\PublishBundle\Form\ApplicationDeployType;
use Netvlies\Bundle\PublishBundle\Form\ApplicationRollbackType;
use Netvlies\Bundle\PublishBundle\Form\ApplicationSetupType;
use Netvlies\Bundle\PublishBundle\Strategy\Commands\ActionFactory;
use Netvlies\Bundle\PublishBundle\Strategy\Commands\CommandApplicationInterface;
use Netvlies\Bundle\PublishBundle\Strategy\Commands\CommandTargetInterface;
use Netvlies\Bundle\PublishBundle\Strategy\Strategy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;
use Netvlies\Bundle\PublishBundle\Entity\Target;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class CommandController
 * @package Netvlies\Bundle\PublishBundle\Controller
 */
class CommandController extends Controller
{

//    /**
//     * @Route("/command/{application}/list")
//     * @Template()
//     * @param Application $application
//     */
//    public function commandPanelAction(Request $request, Application $application)
//    {
//        /**
//         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
//         */
//        $versioningService = $this->get($application->getScmService());
//        $headRevision = $versioningService->getHeadRevision($application);
//
//        $commands = $application->getCommands();
//        $commandFormFactory = $this->container->get('netvlies_publish.commandformfactory');
//        $forms = array();
//
//        foreach ($commands as $command) {
//            /**
//             * @var Command $command
//             */
//
//            $form = $commandFormFactory->createForm($command);
//
//            if ($request->getMethod() == 'POST' && $request->request->has($form->getName())) {
//
//                $form->handleRequest($request);
//
//                if ($form->isValid()) {
//
//                    // Render command
//                    $twig = $this->container->get('netvlies_publish.twig.environment');
//                    $data = $form->getData();
//                    $data['application'] = $application;
//                    $data['versioning'] = $this->container->get($application->getScmService());
//                    $data['approot'] =  $this->get('kernel')->getRootDir();
//                    $data['strategy'] = $this->container->get($application->getDeploymentStrategy());
//
//                    $cmd = $twig->render($command->getId(), $data);
//
//
//                    // Determine follow up action
//                    if ($form->get('preview')->isClicked()) {
//                        return new Response($cmd);
//                    } else {
//
//
//                        // Create commandlog
//                        $target = null;
//
//                        foreach($data as $value) {
//                            if ($value instanceof Target) {
//                                $target = $value;
//                            }
//                        }
//
//                        $commandLog = new CommandLog();
//                        $commandLog->setCommandLabel($command->getLabel());
//                        $commandLog->setCommand($cmd);
//                        $commandLog->setDatetimeStart(new \DateTime());
//
//                        if ($target) {
//                            $commandLog->setHost($target->getEnvironment()->getHostname());
//                            $commandLog->setTarget($target);
//                            $commandLog->setType($target->getEnvironment()->getType());
//                        }
//
//                        if($this->get('security.context')->getToken()->getUser()!='anon.'){
//                            $userName = $this->get('security.context')->getToken()->getUser()->getUsername();
//                        }
//                        else{
//                            $userName = 'anonymous';
//                        }
//
//                        $commandLog->setUser($userName);
//
//                        $em = $this->getDoctrine()->getManager();
//                        $em->persist($command);
//                        $em->flush();
//
//                        return $this->redirect($this->generateUrl('netvlies_publish_command_exec', array('commandlog' => $commandLog->getId())));
//                    }
//                }
//            }
//
//            $forms[] = $form->createView();
//        }
//
//        return array(
//            'headRevision' => $headRevision,
//            'application' => $application,
//            'forms' => $forms
//        );
//    }



    /**
     * @Route("/command/{application}/list")
     * @Template()
     * @param Application $application
     */
    public function commandPanelAction(Application $application)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($application->getScmService());
        $repoPath = $versioningService->getRepositoryPath($application);
        $headRevision = $versioningService->getHeadRevision($application);

        if(!file_exists($repoPath)){
            return $this->redirect($this->generateUrl('netvlies_publish_application_checkoutrepository', array('application' => $application->getId())));
        }


        $commandTemplates = $strategy = $application->getDeploymentStrategy()->getCommandTemplates();


        $twigEnvironment = $this->container->get('twig');

        foreach ($commandTemplates as $template)
        {
            /**
             * @var $template CommandTemplate
             */

            $twigTemplate = $twigEnvironment->createTemplate($template->getTemplate());
            $exceptions = true;
            $context = array();

            while($exceptions) {
                try {
                    $twigTemplate->render($context);
                } catch(\Twig_Error_Runtime $e) {
                    $variable = preg_replace('/Variable "/', '', $e->getRawMessage());
                    $variable = preg_replace('/" does not exist$/', '', $variable);
                    $context[$variable] = new TwigDummy();
                    continue;

                }
                $exceptions = false;

            }

            //@todo so now we have the main keys for the twig template, try to match inbuilt organist variables
            // and create a form for that
            var_dump(array_keys($context));
            exit;
            //$twigEnvironment->getCompiler()->gt


        }

        $context = array(
            'application' => $application,
            'versioning' => $versioningService,
            'revision' => '',
            'target' => '',
            'approot' => $this->get('kernel')->getRootDir(),
            'repositorypath' => $this->container->getParameter('repository_path')
        );



        $actionFactory = new ActionFactory(ucfirst($strategy->getKeyname()));

        $deployCommand = $actionFactory->getDeployCommand();
        $deployCommand->setApplication($application);
        $deployCommand->setVersioningService($versioningService);

        $rollbackCommand = $actionFactory->getRollbackCommand();
        $rollbackCommand->setApplication($application);
        $rollbackCommand->setRepositoryPath($versioningService->getRepositoryPath($application));

        $setupCommand = $actionFactory->getInitCommand();
        $setupCommand->setApplication($application);

        $deployForm = $this->createForm(new ApplicationDeployType(), $deployCommand, array('app' => $application));
        $rollbackForm = $this->createForm(new ApplicationRollbackType(), $rollbackCommand, array('app' => $application));
        $setupForm = $this->createForm(new ApplicationSetupType(), $setupCommand, array('app' => $application));

        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){

            if ($request->request->has($deployForm->getName())){

                $deployForm->handleRequest($request);

                if($deployForm->isValid()){

                    return $this->forward('NetvliesPublishBundle:Command:execTargetCommand', array(
                        'command'  => $deployCommand
                    ));
                }
            }

            if ($request->request->has($rollbackForm->getName())){
                $rollbackForm->handleRequest($request);

                if($rollbackForm->isValid()){

                    return $this->forward('NetvliesPublishBundle:Command:execTargetCommand', array(
                        'command'  => $rollbackCommand
                    ));
                }
            }

            if ($request->request->has($setupForm->getName())){
                $setupForm->handleRequest($request);

                if($setupForm->isValid()){

                    return $this->forward('NetvliesPublishBundle:Command:execTargetCommand', array(
                        'command'  => $setupCommand
                    ));
                }
            }
        }

        return array(
            'application' => $application,
            'deployForm' => $deployForm->createView(),
            'rollbackForm' => $rollbackForm->createView(),
            'setupForm' => $setupForm->createView(),
            'headRevision' => $headRevision
        );
    }

    /**
     * @param CommandTargetInterface $command
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @return Response
     */
    public function execTargetCommandAction(CommandTargetInterface $command)
    {
        $commandLog = new CommandLog();
        $versioningService = $this->container->get($command->getApplication()->getScmService());
        $repoPath = $versioningService->getRepositoryPath($command->getApplication());

        if(!file_exists($repoPath)){
            throw new \Exception('This shouldnt happen! Target cant be executed when repo isnt there. GUI flow should prevent this');
        }

        // Anyterm strips all env vars before executing exec.sh under user deploy
        // So we need to add it manually in order to find the appropiate keys for git repos and remote servers to deploy to
        $script = 'export HOME=' . $_SERVER['HOME'] . ' && ';

        // Change dir to app repository
        $script .='cd '. $repoPath ." && ";

        //@todo extract following hard sets into configurations for different deployment strategies with different type of settings
        switch ($command->getApplication()->getDeploymentStrategy()) {
            case 'capistrano2':
                $script .='source /usr/local/rvm/scripts/rvm && ';
                $script .='rvm use ruby-1.8.7-head && ';
                break;
            case 'capistrano3':
                $script .='source /usr/local/rvm/scripts/rvm && ';
                $script .='rvm use ruby-2.2.1 && ';
                break;
            default:
                break;
        }

        $script .=$command->getCommand();

        $commandLog->setCommandLabel($command->getLabel());
        $commandLog->setCommand($script);
        $commandLog->setDatetimeStart(new \DateTime());
        $commandLog->setHost($command->getTarget()->getEnvironment()->getHostname());
        $commandLog->setTarget($command->getTarget());
        $commandLog->setType($command->getTarget()->getEnvironment()->getType());

        if($this->get('security.context')->getToken()->getUser()!='anon.'){
            $userName = $this->get('security.context')->getToken()->getUser()->getUsername();
        }
        else{
            $userName = 'anonymous';
        }

        $commandLog->setUser($userName);

        /**
         * @var EntityManager $em
         */
        $em  = $this->getDoctrine()->getManager();
        $em->persist($commandLog);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_command_exec', array('commandlog' => $commandLog->getId())));
    }


    /**
     * @param CommandApplicationInterface $command
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
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

        if($this->get('security.context')->getToken()->getUser()!='anon.'){
            $userName = $this->get('security.context')->getToken()->getUser()->getUsername();
        }
        else{
            $userName = 'anonymous';
        }

        $commandLog->setUser($userName);

        /**
         * @var EntityManager $em
         */
        $em  = $this->getDoctrine()->getManager();
        $em->persist($commandLog);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_command_exec', array('commandlog' => $commandLog->getId())));
    }


    /**
     *
     * @Route("/console/exec/{commandlog}")
     * @Template()
     * @param CommandLog $commandlog
     */
    public function execAction(CommandLog $commandlog)
    {
        $console_url = $this->getRequest()->getSchemeAndHttpHost();

        $application = $commandlog->getApplication();

        if($commandlog->getDatetimeEnd()){
            $this->get('session')->getFlashBag()->add('warning', sprintf('This command is already executed. <a href="%s" class="alert-link">Click here</a> if you want to re-execute it', $this->generateUrl('netvlies_publish_command_reexecute', array('commandlog'=>$commandlog->getId()))));
            return $this->redirect($this->generateUrl('netvlies_publish_application_dashboard', array('application' => $application->getId())));
        }

        return array(
            'console_url' => $console_url,
            'console_port' => $this->container->getParameter('netvlies_publish.console_port'),
            'command' => $commandlog,
        );
    }


    /**
     * @Route("/command/viewlog/{commandlog}")
     * @Template()
     */
    public function viewLogAction(CommandLog $commandlog)
    {
        return array(
            'log' => $commandlog
        );
    }


    /**
     * @Route("/command/{application}/logs")
     * @Template()
     * @param Application $application
     */
    public function listLogsAction(Application $application)
    {
        return array(
            'logs' => $this->getDoctrine()->getRepository('NetvliesPublishBundle:CommandLog')->getLogsForApplication($application),
            'application' => $application
        );
    }


    /**
     * @Route("/command/reexecute/{commandlog}")
     * @param CommandLog $commandlog
     * @return Response
     */
    public function reExecuteAction(CommandLog $commandlog)
    {
        $newCommand = new CommandLog();
        $newCommand->setApplication($commandlog->getApplication());
        $newCommand->setTarget($commandlog->getTarget());
        $newCommand->setType($commandlog->getType());
        $newCommand->setCommand($commandlog->getCommand());
        $newCommand->setDatetimeStart(new \DateTime());
        $newCommand->setHost($commandlog->getHost());
        $newCommand->setCommandLabel($commandlog->getCommandLabel());

        if($this->get('security.context')->getToken()->getUser()!='anon.'){
            $userName = $this->get('security.context')->getToken()->getUser()->getUsername();
        }
        else{
            $userName = 'anonymous';
        }

        $newCommand->setUser($userName);

        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $em->persist($newCommand);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_command_exec', array('commandlog' => $newCommand->getId())));
    }


//    /**
//     * @Route("/command/loadchangeset/{target}/{revision}")
//     * @Template()
//     * @param \Netvlies\Bundle\PublishBundle\Entity\Target $target
//     * @param $revision
//     */
//    public function loadChangesetAction(Target $target, $revision)
//    {
//        /**
//         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
//         */
//        $versioningService = $this->get($target->getApplication()->getScmService());
//        $errorMsg = '';
//        $messages = array();
//
//        try{
//            $messages = $versioningService->getChangesets($target->getApplication(), $target->getLastDeployedRevision(), $revision);
//        }
//        catch(\Exception $e){
//            $errorMsg = $e->getMessage();
//        }
//
//        return array(
//            'errorMsg' => $errorMsg,
//            'messages' => $messages
//        );
//    }
}
