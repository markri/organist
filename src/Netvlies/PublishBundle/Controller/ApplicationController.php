<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\ApplicationRepository;
use Netvlies\PublishBundle\Entity\Application;

use Netvlies\PublishBundle\Form\FormApplicationEditType;
use Netvlies\PublishBundle\Form\FormApplicationEnrichType;
use Netvlies\PublishBundle\Form\FormExecuteType;
use Netvlies\PublishBundle\Form\ChoiceList\Branches;





class ApplicationController extends Controller {



    /**
	 * @Route("/application")
	 * @Template()
	 */
    public function indexAction() {
        return array();
    }


    /**
     * Used as embedded controller, to prevent assigning sites, over and over again
     * This controller is called from base2.html.twig So no route is needed
     *
     * @Template()
     */
    public function listAction(){
        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $apps = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->getAll();
        return array('sites' => $apps);
    }


    /**
     *
     * @Route("/application/view/{id}")
     * @Template()
	 */    
    public function viewAction($id, $revision=null) {

        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $sRepositoryPath = $this->container->getParameter('repositorypath');
        $app = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->getApp($id, $sRepositoryPath);


        $query = $oEntityManager->createQuery('
            SELECT d FROM Netvlies\PublishBundle\Entity\Deployment d
            INNER JOIN d.environment e
            WHERE d.application = :app
            ORDER BY e.type, e.hostname
        ');

        $query->setParameter('app', $app);
        $deployments = $query->getResult();

        $allTwigParams = array();
        $allTwigParams['application'] = $app;
        $allTwigParams['deployments'] = $deployments;
        $allTwigParams['revision'] = $revision;

        // Branch / Tag selector form
        $form = $this->createForm(new FormExecuteType(), $app, array('branchchoice' => new Branches($app)));
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){
            $form->bindRequest($request);
        }
        else if(!empty($revision)){
            // == redirected from executeAction, some target is executed, so we need to remember the chosen branch
            // So we simulate a request object and bind it to the form object
            $simrequest = new \Symfony\Component\HttpFoundation\Request(array(), array('netvlies_publishbundle_executetype' => array('branchtodeploy'=>$revision)));
            $simrequest->setMethod('POST');
            $form->bindRequest($simrequest);
        }

        $allTwigParams['form'] = $form->createView();

        return $allTwigParams;
    }

    /**
     *
     * @Route("/application/execute/{id}/{deployid}/{revision}")
     * @Route("/application/execute/{id}/{deployid}")
     *
     * @Template("NetvliesPublishBundle:Application:view.html.twig")
	 */
    public function executeAction($id, $deployid, $revision=null){
        $twigParams = $this->viewAction($id, $revision);
        $twigParams['deployid'] = $deployid;
        $twigParams['revision'] = $revision;
        return $twigParams;
    }


    /**
     * @Route("/application/edit/{id}")
     * @Template()
     */
    public function editAction($id){

        $em  = $this->getDoctrine()->getEntityManager();
        $sRepositoryPath = $this->container->getParameter('repositorypath');
        $app = $em->getRepository('NetvliesPublishBundle:Application')->getApp($id, $sRepositoryPath);
        $currentRepo = $app->getGitrepo();
        $form = $this->createForm(new FormApplicationEditType(), $app);
        $request = $this->getRequest();


        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
                $em->persist($app);
                $em->flush();

                $newRepo = $app->getGitrepo();
                if($currentRepo == $newRepo){
                    return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$id)));
                }
                else{
                    return $this->redirect($this->generateUrl('netvlies_publish_git_cloneapplication', array('id'=>$id)));
                }
            }
        }

        return array(
            'form' => $form->createView(),
            'application' => $app
        );
    }

    /**
     * @Route("/application/enrich/{id}")
     * @Template()
     */
    public function enrichAction($id){

        $em  = $this->getDoctrine()->getEntityManager();
        $sRepositoryPath = $this->container->getParameter('repositorypath');
        $app = $em->getRepository('NetvliesPublishBundle:Application')->getApp($id, $sRepositoryPath);
        $form = $this->createForm(new FormApplicationEnrichType(), $app);
        $request = $this->getRequest();


        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
                $em->persist($app);
                $em->flush();

                // Create git repo, add basic files
                //@todo based on type

                switch($app->getType()){
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
}
