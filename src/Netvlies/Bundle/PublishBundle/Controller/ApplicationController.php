<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

use Netvlies\Bundle\PublishBundle\Action\CheckoutCommand;
use Netvlies\Bundle\PublishBundle\Entity\UserFile;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use GitElephant\Repository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Netvlies\Bundle\PublishBundle\Entity\CommandLogRepository;
use Netvlies\Bundle\PublishBundle\Entity\Application;

use Netvlies\Bundle\PublishBundle\Form\ApplicationCreateType;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationEditType;


class ApplicationController extends Controller {


    /**
     * @Route("/application/create")
     * @Template()
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $application = new Application();

        $form = $this->createForm(
            new ApplicationCreateType(),
            $application
        );

        if($request->getMethod()=='POST'){
            $form->bind($request);

            if($form->isValid()){
                $em = $this->container->get('doctrine.orm.entity_manager');
                $appTypes = $this->container->getParameter('netvlies_publish.applicationtypes');

                if(isset($appTypes[$application->getApplicationType()]['userfiles'])){
                    foreach($appTypes[$application->getApplicationType()]['userfiles'] as $sharedFile){
                        $userFile = new UserFile();
                        $userFile->setApplication($application);
                        $userFile->setPath($sharedFile);
                        $userFile->setType('F');
                        $application->addUserFile($userFile);
                    }
                }
                if(isset($appTypes[$application->getApplicationType()]['userdirs'])){
                    foreach($appTypes[$application->getApplicationType()]['userdirs'] as $sharedDir){
                        $userFile = new UserFile();
                        $userFile->setApplication($application);
                        $userFile->setPath($sharedDir);
                        $userFile->setType('D');
                        $application->addUserFile($userFile);
                    }
                }

                $em->persist($application);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', sprintf('Application %s is succesfully created', $application->getName()));
                return $this->redirect($this->generateUrl('netvlies_publish_application_dashboard', array('id'=>$application->getId())));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }


    /**
     * Dashboard view
     *
     * @Route("/application/dashboard/{id}")
     * @Template()
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @param Application $application
     * @return array
     */
    public function dashboardAction(Application $application)
    {
        /**
         * @var CommandLogRepository
         */
        $logRepo = $this->getDoctrine()->getManager()->getRepository('NetvliesPublishBundle:CommandLog');
        $logs = $logRepo->getLogsForApplication($application, 5);

        return array(
            'application' => $application,
            'logs' => $logs
        );
    }



    /**
     * @Route("/application/settings/{id}")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @Template()
     * @param $application Application
     * @return array
     */
    public function editAction($application)
    {
        $form = $this->createForm(new FormApplicationEditType(), $application);
        $request = $this->getRequest();

        $originalUserFiles = clone $application->getUserFiles();

        if($request->getMethod() == 'POST'){

            $form->bind($request);

            if($form->isValid()){
                $em = $this->container->get('doctrine.orm.entity_manager');

                foreach($application->getUserFiles() as $userFile){
                    foreach($originalUserFiles as $key=>$origUserFile){
                        if($userFile->getId() == $origUserFile->getId()){
                            $originalUserFiles->remove($key);
                        }
                    }
                }

                foreach($originalUserFiles as $deleteUserFile){
                    $em->remove($deleteUserFile);
                }

                $em->persist($application);
                $em->flush();

                // Redirect to same edit page
                $this->get('session')->getFlashBag()->add('success', sprintf('Application %s is updated', $application->getName()));
                return $this->redirect($this->generateUrl('netvlies_publish_application_edit', array('id'=>$application->getId())));
            }
        }

        $deletable = $application->getTargets()->count() == 0 && $application->getUserFiles()->count() == 0;

        return array(
            'form' => $form->createView(),
            'application' => $application,
            'deleteable' => $deletable
        );
    }

    /**
     * @Route("/application/delete/{id}")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @param $application Application
     */
    public function deleteAction($application)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($application);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_default_index'));
    }


    /**
     * This action is used as subaction to load all available applications into its template.
     *
     * @Route("/application/list/widget")
     * @Template()
     */
    public function listWidgetAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $apps = $em->getRepository('NetvliesPublishBundle:Application')->getAll();

        return array('apps' => $apps);
    }


    /**
     * @Route("/application/{id}/checkoutrepository")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @param Application $application
     */
    public function checkoutRepository($application)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($application->getScmService());

        if(file_exists($versioningService->getRepositoryPath($application))){
            return $this->redirect($this->generateUrl('netvlies_publish_application_updaterepository', array('id' => $application->getId())));
        }

        $command = new CheckoutCommand();
        $command->setApplication($application);

        return $this->forward('NetvliesPublishBundle:Command:execApplicationCommand', array(
            'command'  => $command
        ));
    }

    /**
     * @Route("/application/{id}/updaterepository")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @param Application $application
     */
    public function updateRepositoryAction($application)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($application->getScmService());

        if(!file_exists($versioningService->getRepositoryPath($application))){
            return $this->redirect($this->generateUrl('netvlies_publish_application_checkoutrepository', array('id' => $application->getId())));
        }

        $this->get('session')->getFlashBag()->add('success', sprintf('Repository for %s is updated', $application->getName()));

        return $this->redirect($this->generateUrl('netvlies_publish_command_commandpanel', array('id' => $application->getId())));
    }



}
