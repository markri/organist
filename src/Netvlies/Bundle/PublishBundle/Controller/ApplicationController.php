<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Netvlies\Bundle\PublishBundle\Entity\ApplicationRepository;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\ConsoleAction;
use Netvlies\Bundle\PublishBundle\Form\ApplicationCreateType;

use Netvlies\Bundle\PublishBundle\Form\FormApplicationEditType;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationEnrichType;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationDeployType;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationRollbackType;
use Netvlies\Bundle\PublishBundle\Form\FormApplicationDeployOType;
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
     * This action is used as subaction to load all available applications into its template, which is almost always used
     *
     * @Route("/application/list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $apps = $em->getRepository('NetvliesPublishBundle:Application')->getAll();
        return array('apps' => $apps);
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
         * @var \Netvlies\Bundle\PublishBundle\Services\Scm\ScmInterface $scmService
         */
        $scmService = $this->get($application->getScmService());

        if(!file_exists($scmService->getRepositoryPath($application))){
            try{
                $scmService->checkoutRepository($application);
            }
            catch(\Exception $e){
                $this->get('session')->getFlashBag()->add('error', sprintf('Couldnt update repo for %s. Please check your application config', $application->getName()));
                return $this->redirect($this->generateUrl('netvlies_publish_application_dashboard', array('id' => $application->getId())));
            }
        }

        $scmService->updateRepository($application);
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
         * @var \Netvlies\Bundle\PublishBundle\Services\Scm\ScmInterface $scmService
         */
        $scmService = $this->get($application->getScmService());
        $repoPath = $scmService->getRepositoryPath($application);

        if(!file_exists($repoPath)){
            return $this->redirect($this->generateUrl('netvlies_publish_application_updaterepository', array('id' => $application->getId())));
        }


        $consoleAction = new ConsoleAction();
        $consoleAction->setContainer($this->container);
        $consoleAction->setApplication($application);

        $deployForm = $this->createForm(new FormApplicationDeployType(), $consoleAction, array('app'=>$application));
        $rollbackForm = $this->createForm(new FormApplicationRollbackType(), $consoleAction, array('app'=>$application));

        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){


            if ($request->request->has($deployForm->getName())){

                $deployForm->bind($request);
                $consoleAction->setCommand($application->getType()->getDeployCommand());


                var_dump($consoleAction->getTarget());
                var_dump($consoleAction->getRevision());
                var_dump($_POST);


                if($deployForm->isValid()){
                    echo 'valid';
                    exit;

                    $target = $consoleAction->getTarget();
                    $target->setLastDeployedBranch($consoleAction->getRevision());
                    $target->setLastDeployedRevision($consoleAction->getRevision());
                    $em->persist($target);
                    $em->flush();

                    return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
                        'consoleAction'  => $consoleAction
                    ));
                }
            }
            exit;

            if ($request->request->has($rollbackForm->getName())){
                $rollbackForm->bind($request);
                $consoleAction->setCommand($application->getType()->getRollbackCommand());
                if($rollbackForm->isValid()){
                    return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
                        'consoleAction'  => $consoleAction
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
