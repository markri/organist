<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\ApplicationRepository;
use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\ScriptBuilder;

use Netvlies\PublishBundle\Form\FormApplicationEditType;
use Netvlies\PublishBundle\Form\FormApplicationEnrichType;
use Netvlies\PublishBundle\Form\FormExecuteType;
use Netvlies\PublishBundle\Form\ChoiceList\Branches;





class ApplicationController extends Controller {



    /**
	 * @Route("/")
	 * @Template()
	 */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/application/list")
     * @Template()
     */
    public function listAction(){
        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $apps = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->getAll();
        return array('apps' => $apps);
    }


    /**
     *
     * @Route("/application/{id}/targetmappings")
     * @Template()
	 */    
    public function targetMappingsAction($id, $revision=null) {

        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $sRepositoryPath = $this->container->getParameter('repositorypath');

        /**
         * @var \Netvlies\PublishBundle\Entity\Application $app
         */
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

        // Git reference selector form
        $form = $this->createForm(new FormExecuteType(), $app, array('branchchoice' => new Branches($app)));
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){
            $form->bindRequest($request);
            $bbuser = $this->container->getParameter('bitbucketuser');
            $bbpw = $this->container->getParameter('bitbucketpassword');
            $app->processBitbucketReference($bbuser, $bbpw);
        }
        else if(!empty($revision)){
            //@todo ugly place to implement this
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
     * @Route("/application/{id}/view")
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
        $scriptBuilder = new \Netvlies\PublishBundle\Entity\ScriptBuilder(time());

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
     * This will load a template with an iframe where a console is loaded with params below
	 
     * @Route("/application/{id}/execute/{deployid}/{revision}")
     * @Route("/application/{id}/execute/{deployid}")
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

        $scriptBuilder = new ScriptBuilder(time());

        return array(
            'form' => $form->createView(),
            'application' => $app,
            'gitupdatescript' => $scriptBuilder->getGitUpdateScript($app)
        );
    }

    /**
     * @Route("/application/enrich/{id}")
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
}
