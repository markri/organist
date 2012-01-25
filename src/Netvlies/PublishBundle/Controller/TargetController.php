<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\Target;
use Netvlies\PublishBundle\Form\FormTargetStep1Type;
use Netvlies\PublishBundle\Form\FormTargetStep2Type;
use Netvlies\PublishBundle\Form\ChoiceList\EnvironmentsType;




class TargetController extends Controller {


    /**
     * @Route("/application/{id}/target/new/step1")
     * @Template()
     */
    public function createStep1Action($id){

        $em  = $this->getDoctrine()->getEntityManager();
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

		// Fill target with default values which are most likely to be true
        $target = new Target();
        $target->setApplication($app);

        return $this->handleFormStep1($target);
    }

    /**
     * @Route("/target/new/step2")
     * @Template()
     */
    public function createStep2Action(){

        $session = $this->getRequest()->getSession();
        /**
         * @var \Netvlies\PublishBundle\Entity\Target $target
         */
        $target = $session->get('target');


        /**
         * @var \Netvlies\PublishBundle\Entity\Application $app
         */
        $app = $target->getApplication();


        // Update phing targets through repo
        $em  = $this->getDoctrine()->getEntityManager();
        $em->getRepository('NetvliesPublishBundle:PhingTarget')->updatePhingTargets($app);

		// Fill target with default values which are most likely to be true

        switch($app->getType()->getName()){
            case 'symfony2':
                $target->setWebroot('/home/'.$app->getName().'/vhost/web'.$app->getName());
                $target->setApproot('/home/'.$app->getName().'/vhost/'.$app->getName());
                break;
            default:
                $target->setWebroot('/home/'.$app->getName().'/vhost/'.$app->getName());
                $target->setApproot('/home/'.$app->getName().'/vhost/'.$app->getName());
                break;
        }

        $target->setApplication($app);
        $target->setUsername($app->getName());
        $target->setMysqldb($app->getName());
        $target->setMysqluser($app->getName());
        $target->setMysqlpw($app->getMysqlpw());

        return $this->handleFormStep2($target);
    }

    /**
     * @Route("/target/edit/{id}")
     * @Template()
     */
    public function editAction($id){
        $em  = $this->getDoctrine()->getEntityManager();
        $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($id);

        return $this->handleForm($target);
    }


    /**
     *
     * @Route("/target/delete/{id}")
     *
     */
    public function deleteAction($id){
        $em  = $this->getDoctrine()->getEntityManager();
        $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($id);
        $app = $target->getApplication();
        $em->remove($target);
        $em->flush();

        return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$app->getId())));
    }
	
	
//    protected function handleForm($deployment){
//
//
//        $form = $this->createForm(new FormDeploymentStep1Type(), $deployment, array('app' => $deployment->getApplication()));
//
//        $request = $this->getRequest();
//        $em  = $this->getDoctrine()->getEntityManager();
//        $app = $deployment->getApplication();
//
//
//
//        if($request->getMethod() == 'POST'){
//
//            $form->bindRequest($request);
//
//            if($form->isValid()){
//                $em->persist($deployment);
//                $em->flush();
//                return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$app->getId())));
//            }
//        }
//
//        return array(
//            'form' => $form->createView(),
//        );
//    }

    protected function handleFormStep1($target){

		$em  = $this->getDoctrine()->getEntityManager();
		
		$envChoice = new EnvironmentsType($em);
        $form = $this->createForm(new FormTargetStep1Type(), $target, array('app' => $target->getApplication(), 'envchoice'=>$envChoice));

        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
				$session = $this->getRequest()->getSession();
				$session->set('target', $target);
                return $this->redirect($this->generateUrl('netvlies_publish_target_createstep2'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }


    protected function handleFormStep2($target){

        $form = $this->createForm(new FormTargetStep2Type(), $target, array('app' => $target->getApplication()));
        $request = $this->getRequest();
        $em  = $this->getDoctrine()->getEntityManager();
        $app = $target->getApplication();

        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
                $em->persist($target);
                $em->flush();
                return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$app->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }





}
