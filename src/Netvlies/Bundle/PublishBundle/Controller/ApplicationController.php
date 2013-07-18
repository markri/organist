<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

use Netvlies\Bundle\PublishBundle\Action\DeployCommand;
use Netvlies\Bundle\PublishBundle\Action\RollbackCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Form\ApplicationCreateType;

use Netvlies\Bundle\PublishBundle\Form\FormApplicationEditType;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationDeployType;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationRollbackType;
use Netvlies\Bundle\PublishBundle\Form\ChoiceList\BrancheList;
use GitElephant\Repository;


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
        return array(
            'application' => $application
        );
    }



    /**
     * @Route("/application/edit/{id}")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @Template()
     * @param $application Application
     * @return array
     */
    public function editAction($application)
    {
        $form = $this->createForm(new FormApplicationEditType(), $application);
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){

            $form->bind($request);

            if($form->isValid()){
                $em = $this->container->get('doctrine.orm.entity_manager');
                $em->persist($application);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', sprintf('Application %s is updated', $application->getName()));
                return $this->redirect($this->generateUrl('netvlies_publish_application_dashboard', array('id'=>$application->getId())));
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
     * Will return a list of all targets for this application
     *
     * @Route("/application/{id}/targets")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @Template()
	 */
    public function targetsAction($application)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $targets = $em->getRepository('NetvliesPublishBundle:Target')->getOrderedByOTAP($application);

        return array(
            'application' => $application,
            'targets' => $targets
        );
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

        return $this->redirect($this->generateUrl('netvlies_publish_application_controlpanel', array('id' => $application->getId())));
    }

    /**
     * @Route("/application/{id}/controlpanel")
     * @ParamConverter("application", class="NetvliesPublishBundle:Application")
     * @Template()
     * @param Application $application
     */
    public function controlPanelAction($application)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->get($application->getScmService());
        $repoPath = $versioningService->getRepositoryPath($application);

        if(!file_exists($repoPath)){
            return $this->redirect($this->generateUrl('netvlies_publish_application_updaterepository', array('id' => $application->getId())));
        }

        $deployCommand = new DeployCommand();
        $deployCommand->setApplication($application);
        $deployCommand->setRepositoryPath($versioningService->getRepositoryPath($application));


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

                    return $this->forward('NetvliesPublishBundle:Console:execCommand', array(
                        'command'  => $deployCommand
                    ));
                }
            }

            if ($request->request->has($rollbackForm->getName())){
                $rollbackForm->bind($request);

                if($rollbackForm->isValid()){

                    return $this->forward('NetvliesPublishBundle:Console:execCommand', array(
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

}
