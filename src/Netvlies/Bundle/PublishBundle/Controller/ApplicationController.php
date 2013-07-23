<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

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
        $targets = $application->getTargets();
        /**
         * @var CommandLogRepository
         */
        $logRepo = $this->getDoctrine()->getManager()->getRepository('NetvliesPublishBundle:CommandLog');
        $logs = $logRepo->getLogsByTargets($targets, 5);


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
            try{
                $versioningService->checkoutRepository($application);
            }
            catch(\Exception $e){
                $this->get('session')->getFlashBag()->add('error', sprintf('Couldnt update repo for %s. Please check your application config', $application->getName()));
                return $this->redirect($this->generateUrl('netvlies_publish_application_dashboard', array('id' => $application->getId())));
            }
        }

        $versioningService->updateRepository($application);
        $this->get('session')->getFlashBag()->add('success', sprintf('Repository for %s is updated', $application->getName()));

        return $this->redirect($this->generateUrl('netvlies_publish_command_commandpanel', array('id' => $application->getId())));
    }



}
