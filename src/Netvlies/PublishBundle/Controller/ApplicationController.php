<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\ApplicationRepository;
use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\Deployment;
use Netvlies\PublishBundle\Entity\Rollback;

use Netvlies\PublishBundle\Form\FormApplicationEditType;
use Netvlies\PublishBundle\Form\FormApplicationEnrichType;

use Netvlies\PublishBundle\Form\FormApplicationDeployType;
use Netvlies\PublishBundle\Form\FormApplicationRollbackType;
use Netvlies\PublishBundle\Form\ChoiceList\BranchesType;


class ApplicationController extends Controller {



    /**
     * This action is used as subaction to load all available applications into its template, which is almost always used
     *
     * @Route("/application/list")
     * @Template()
     */
    public function listAction(){
        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $apps = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->getAll();
        return array('apps' => $apps);
    }

    /**
     * Will return a list of all targets for this application
     *
     * @Route("/application/{id}/targets")
     * @Template()
	 */    
    public function targetsAction($id) {

        $oEntityManager = $this->getDoctrine()->getEntityManager();

        /**
         * @var \Netvlies\PublishBundle\Entity\Application $app
         */
        $app = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        $targets = $oEntityManager->getRepository('NetvliesPublishBundle:Target')->getOrderedByOTAP($app);

        $allTwigParams = array();
        $allTwigParams['application'] = $app;
        $allTwigParams['targets'] = $targets;

        return $allTwigParams;
    }

    /**
     * Detailed view
     *
     * @Route("/application/{id}/view")
     * @Template()
	 */
    public function viewAction($id) {

        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $app = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        $allTwigParams = array();
        $allTwigParams['application'] = $app;

        return $allTwigParams;
    }


    /**
     * @Route("/application/{id}/edit")
     * @Template()
     */
    public function editAction($id){

        $em  = $this->getDoctrine()->getEntityManager();
        /**
         * @var \Netvlies\PublishBundle\Entity\Application $app
         */
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);
        $currentReference = $app->getReferenceToFollow();

        $gitService = $this->get('git');
        $gitService->setApplication($app);
        $remoteBranches = $gitService->getRemoteBranches();

        $form = $this->createForm(new FormApplicationEditType(), $app, array('branchchoice' => new BranchesType($remoteBranches)));
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){

                $newReference = $app->getReferenceToFollow();
                $app->setBranchToFollow($remoteBranches[$newReference]);
                $this->getRequest()->getSession()->remove('remoteBranches');

                $em->persist($app);
                $em->flush();

                if($currentReference == $newReference){
                    return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$id)));
                }
                else{
                    return $this->redirect($this->generateUrl('netvlies_publish_git_checkout', array('id'=>$id, 'reference'=>$newReference)));
                }
            }
        }

        return array(
            'form' => $form->createView(),
            'application' => $app,
        );
    }

    /**
     * @Route("/application/{id}/enrich")
     * @Template()
     */
    public function enrichAction($id){

        $em  = $this->getDoctrine()->getEntityManager();
        $sRepositoryPath = $this->container->getParameter('repositorypath');

        /**
         * @var \Netvlies\PublishBundle\Entity\Application $app
         */
        $app = $em->getRepository('NetvliesPublishBundle:Application')->getApp($id, $sRepositoryPath);
        $form = $this->createForm(new FormApplicationEnrichType(), $app);
        $request = $this->getRequest();


        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
                $em->persist($app);
                $em->flush();

                // Create git repo, add basic files
                //@todo Do initialization
                $type = $app->getType()->getName();

                switch($type){
                    case 'OMS':

                        break;
                    case 'Symfony2':
                        // execute symfony.sh from command dir to create and import new symfony2 project

                        break;
                    case 'Basissite v1':

                        break;
                    case 'Custom':

                        break;
                }
            }
        }

        return  array(
            'form' => $form->createView(),
            'application' => $app
        );

    }


    /**
     * @Route("/application/{id}/dashboard")
     * @Template()
     */
    public function dashboardAction($id){

        $em  = $this->getDoctrine()->getEntityManager();
        /**
         * @var \Netvlies\PublishBundle\Entity\Application $app
         */
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        $gitService = $this->get('git');
        $gitService->setApplication($app);
        $remoteBranches = $gitService->getRemoteBranches(true);
        $deployment = new Deployment();
        $rollback = new Rollback();

        $deployForm = $this->createForm(new FormApplicationDeployType(), $deployment, array('branchchoice' => new BranchesType($remoteBranches), 'app'=>$app));
        $rollbackForm = $this->createForm(new FormApplicationRollbackType(), $rollback, array('app'=>$app));
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){

            if ($request->request->has($deployForm->getName())){
                $deployForm->bindRequest($request);
                if($deployForm->isValid()){
                    return $this->forward('NetvliesPublishBundle:Console:deploy', array(
                        'deployment'  => $deployment
                    ));
                }
            }

            if ($request->request->has($rollbackForm->getName())){
                $rollbackForm->bindRequest($request);
                if($rollbackForm->isValid()){
                    return $this->forward('NetvliesPublishBundle:Console:rollback', array(
                        'rollback'  => $rollback
                    ));
                }
            }
        }

        return array(
            'deployForm' => $deployForm->createView(),
            'rollbackForm' => $rollbackForm->createView(),
            'application' => $app,
        );
    }
}
