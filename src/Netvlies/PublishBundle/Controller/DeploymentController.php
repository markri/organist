<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\Deployment;
use Netvlies\PublishBundle\Form\FormDeploymentStep1Type;
use Netvlies\PublishBundle\Form\ChoiceList\EnvironmentsType;




class DeploymentController extends Controller {


    /**
     * @Route("/deployment/app/{id}/new/step1")
     * @Template()
     */
    public function createStep1Action($id){

        $em  = $this->getDoctrine()->getEntityManager();
        $sRepositoryPath = $this->container->getParameter('repositorypath');
        $app = $em->getRepository('NetvliesPublishBundle:Application')->getApp($id, $sRepositoryPath);


		// Fill deployment with default values which are most likely to be true
        $deployment = new Deployment();
        $deployment->setApplication($app);

        return $this->handleFormStep1($deployment);
    }

    /**
     * @Route("/deployment/app/{id}/new/step2")
     * @Template()
     */
    public function createStep2Action($id){

        $em  = $this->getDoctrine()->getEntityManager();
        $sRepositoryPath = $this->container->getParameter('repositorypath');

        /**
         * @var \Netvlies\PublishBundle\Entity\Application $app
         */
        $app = $em->getRepository('NetvliesPublishBundle:Application')->getApp($id, $sRepositoryPath);


        // Update phing targets through repo
        $em->getRepository('NetvliesPublishBundle:PhingTarget')->updatePhingTargets($app);

		// Fill deployment with default values which are most likely to be true
        $deployment = $this->getRequest()->getSession()->get('deployment');

        switch($app->getType()->getName()){
            case 'symfony2':
                $deployment->setWebroot('/home/'.$app->getName().'/vhost/web'.$app->getName());
                $deployment->setApproot('/home/'.$app->getName().'/vhost/'.$app->getName());
                break;
            default:
                $deployment->setWebroot('/home/'.$app->getName().'/vhost/'.$app->getName());
                $deployment->setApproot('/home/'.$app->getName().'/vhost/'.$app->getName());
                break;
        }

        $deployment->setApplication($app);
        $deployment->setUsername($app->getName());
        $deployment->setMysqldb($app->getName());
        $deployment->setMysqluser($app->getName());
        $deployment->setMysqlpw($app->getMysqlpw());

        return $this->handleFormStep2($deployment);
    }

    /**
     * @Route("/deployment/edit/{id}")
     * @Template()
     */
    public function editAction($id){
        $em  = $this->getDoctrine()->getEntityManager();
        $deployment = $em->getRepository('NetvliesPublishBundle:Deployment')->findOneById($id);

        return $this->handleForm($deployment);
    }


    /**
     *
     * @Route("deployment/delete/{id}")
     *
     */
    public function deleteAction($id){
        $em  = $this->getDoctrine()->getEntityManager();
        $deployment = $em->getRepository('NetvliesPublishBundle:Deployment')->findOneById($id);
        $app = $deployment->getApplication();
        $em->remove($deployment);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$app->getId())));
    }
	
	
    protected function handleForm($deployment){


        $form = $this->createForm(new FormDeploymentStep1Type(), $deployment, array('app' => $deployment->getApplication()));

        $request = $this->getRequest();
        $em  = $this->getDoctrine()->getEntityManager();
        $app = $deployment->getApplication();



        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
                $em->persist($deployment);
                $em->flush();
                return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$app->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }	

    protected function handleFormStep1($deployment){

		$em  = $this->getDoctrine()->getEntityManager();
		
		$envChoice = new EnvironmentsType($em);
		
        $form = $this->createForm(new FormDeploymentStep1Type(), $deployment, array('app' => $deployment->getApplication(), 'envchoice'=>$envChoice));
        $request = $this->getRequest();

        $app = $deployment->getApplication();

        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
				$session = $this->getRequest()->getSession();
				$session->set('deployment', $deployment);
                return $this->redirect($this->generateUrl('netvlies_publish_deployment_createstep2', array('id'=>$app->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }


    protected function handleFormStep2($deployment){

        $form = $this->createForm(new FormDeploymentStep2Type(), $deployment, array('app' => $deployment->getApplication()));
        $request = $this->getRequest();
        $em  = $this->getDoctrine()->getEntityManager();
        $app = $deployment->getApplication();

        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
                $em->persist($deployment);
                $em->flush();
                return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$app->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }





}
