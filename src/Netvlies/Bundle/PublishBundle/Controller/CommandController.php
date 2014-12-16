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

use Netvlies\Bundle\PublishBundle\Action\CommandApplicationInterface;
use Netvlies\Bundle\PublishBundle\Action\CommandTargetInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishBundle\Action\DeployCommand;
use Netvlies\Bundle\PublishBundle\Action\RollbackCommand;
use Netvlies\Bundle\PublishBundle\Form\ApplicationDeployType;
use Netvlies\Bundle\PublishBundle\Form\ApplicationRollbackType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CommandController extends Controller
{

    /**
     * @Route("/application/{application}/commands")
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

        $deployCommand = new DeployCommand();
        $deployCommand->setApplication($application);
        $deployCommand->setVersioningService($versioningService);


        $rollbackCommand = new RollbackCommand();
        $rollbackCommand->setApplication($application);
        $rollbackCommand->setRepositoryPath($versioningService->getRepositoryPath($application));

        $deployForm = $this->createForm(new ApplicationDeployType(), $deployCommand, array('app'=>$application));
        $rollbackForm = $this->createForm(new ApplicationRollbackType(), $rollbackCommand, array('app'=>$application));

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
        }

        return array(
            'deployForm' => $deployForm->createView(),
            'rollbackForm' => $rollbackForm->createView(),
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
        $script = 'export HOME='.$_SERVER['HOME'].' && ';

        // Change dir to app repository
        $script .='cd '. $repoPath ." && ";
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
     * This route is fixed! Due to apache/nginx proxy setting that will redirect /console/exec/anyterm to appropriate assets
     * This action should never be called without having used the prepareCommand (which will prepare a log entry)
     *
     * @Route("/console/exec/{commandlog}")
     * @Template()
     * @param CommandLog $commandlog
     */
    public function execAction(CommandLog $commandlog)
    {
        $application = $commandlog->getApplication();

        if($commandlog->getDatetimeEnd()){
            $this->get('session')->getFlashBag()->add('warning', sprintf('This command is already executed. <a href="%s" class="alert-link">Click here</a> if you want to re-execute it', $this->generateUrl('netvlies_publish_command_reexecute', array('commandlog'=>$commandlog->getId()))));
            return $this->redirect($this->generateUrl('netvlies_publish_application_dashboard', array('application' => $application->getId())));
        }

        return array(
            'command' => $commandlog
        );
    }


    /**
     * @Route("/console/{application}/viewlog/{commandlog}")
     * @Template()
     */
    public function viewLogAction(Application $application, CommandLog $commandlog)
    {
        return array(
            'log' => $commandlog
        );
    }


    /**
     * @Route("/console/logs/{application}")
     * @Template()
     * @param Application $application
     */
    public function listLogsAction(Application $application)
    {
        return array(
            'logs' => $this->getDoctrine()->getRepository('NetvliesPublishBundle:CommandLog')->getLogsForApplication($application)
        );
    }


    /**
     * @Route("/console/reexecute/{commandlog}")
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


    /**
     * @Route("/command/loadchangeset/{target}/{revision}")
     * @Template()
     * @param \Netvlies\Bundle\PublishBundle\Entity\Target $target
     * @param $revision
     */
    public function loadChangesetAction(Target $target, $revision)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($target->getApplication()->getScmService());
        $errorMsg = '';
        $messages = array();

        try{
            $messages = $versioningService->getChangesets($target->getApplication(), $target->getLastDeployedRevision(), $revision);
        }
        catch(\Exception $e){
            $errorMsg = $e->getMessage();
        }

        return array(
            'errorMsg' => $errorMsg,
            'messages' => $messages
        );
    }
}
