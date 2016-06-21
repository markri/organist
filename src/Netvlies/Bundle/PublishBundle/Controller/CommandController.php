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

use Netvlies\Bundle\PublishBundle\Entity\CommandTemplate;
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishBundle\Strategy\Commands\CommandApplicationInterface;
use Netvlies\Bundle\PublishBundle\Strategy\Commands\CommandTargetInterface;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class CommandController
 * @package Netvlies\Bundle\PublishBundle\Controller
 */
class CommandController extends Controller
{
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
        $forms = array();
        $formFactory = $this->container->get('netvlies_publish.commandformfactory');


        // Form generation is based on missing template variables
        // @todo currently there is no way to order fields, or to add custom fields like foreign keys
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

            $variables = array_keys($context);

            $forms[$template->getId()] = $formFactory->createForm($template, $application, $variables);
        }

        $context = array(
            'application' => $application,
            'versioning' => $versioningService,
            'revision' => '',
            'target' => '',
            'approot' => $this->get('kernel')->getRootDir(),
            'repositorypath' => $this->container->getParameter('repository_path')
        );


        $request = $this->getRequest();
        $em = $this->get('doctrine.orm.entity_manager');

        if($request->getMethod() == 'POST'){

            foreach ($forms as $id => $form) {
                /**
                 * @var $form Form
                 */

                if ($request->request->has($form->getName())) {
                    $form->handleRequest($request);

                    if($form->isValid()){
                        /**
                         *
                         */
                        $template = $em->getRepository('NetvliesPublishBundle:CommandTemplate')->find($id);
                        $context = array_merge($context, $form->getData());

                        return $this->forward('NetvliesPublishBundle:Command:execTargetCommand', array(
                              'template' => $template,
                              'context' => $context
                        ));
                    }
                }
            }
        }

        $formViews = array();
        foreach ($forms as $form)
        {
            $formViews[] = $form->createView();
        }

        return array(
            'application' => $application,
            'forms' => $formViews,
            'headRevision' => $headRevision
        );
    }

    /**
     * @param CommandTargetInterface $command
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @return Response
     */
    public function execTargetCommandAction(CommandTemplate $template, array $context)
    {
        /**
         * @var Application $application
         */
        $application = $context['application'];

        /**
         * @var VersioningInterface $versioningService
         */
        $versioningService = $context['versioning'];

        /**
         * @var Target $target;
         */
        $target = $context['target'];

        $commandLog = new CommandLog();

        $repoPath = $versioningService->getRepositoryPath($application);

        if(!file_exists($repoPath)){
            throw new \Exception('This shouldnt happen! Target cant be executed when repo isnt there. GUI flow should prevent this');
        }

        $twigEnvironment = $this->container->get('twig');
        $twigTemplate = $twigEnvironment->createTemplate($template->getTemplate());
        $script = $twigTemplate->render($context);

        $commandLog->setCommandLabel($template->getTitle());
        $commandLog->setCommand($script);
        $commandLog->setDatetimeStart(new \DateTime());
        $commandLog->setHost($target->getEnvironment()->getHostname());
        $commandLog->setTarget($target);
        $commandLog->setType($target->getEnvironment()->getType());

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
     * @Route("/command/exec/{commandlog}")
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
}
