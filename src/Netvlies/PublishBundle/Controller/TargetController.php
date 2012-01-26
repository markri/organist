<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\Target;
use Netvlies\PublishBundle\Form\FormTargetType;
use Netvlies\PublishBundle\Form\ChoiceList\EnvironmentsType;




class TargetController extends Controller {


    /**
     * @Route("/application/{id}/target/new")
     * @Template()
     */
    public function createAction($id){

        $em  = $this->getDoctrine()->getEntityManager();
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        return $this->handleForm($app);
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

        return $this->redirect($this->generateUrl('netvlies_publish_application_targets', array('id'=>$app->getId())));
    }
	


    /**
     * @todo validation is now done by HTML5 required attributes, which is ok, but may fail when relying on SSP validation, we dont have validation groups
     * @param $app \Netvlies\PublishBundle\Entity\Application
     * @return array
     */
    protected function handleForm($app){

		$em  = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $target = new Target();
        $target->setApplication($app);

		$envChoice = new EnvironmentsType($em);

        $pbag = $request->request->all();
        $secondPart = !empty($pbag['netvlies_publishbundle_targettype']['label']);

        $form = $this->createForm(new FormTargetType(), $target, array('app' => $target->getApplication(), 'envchoice'=>$envChoice, 'secondPart'=>$secondPart));

        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            // This is still an id, because we use a choicelist in order to get an ordered list of envs by O, T, A, P
            $envId = $target->getEnvironment();
            /**
             * @var \Netvlies\PublishBundle\Entity\Environment $env
             */
            $env = $em->getRepository('NetvliesPublishBundle:Environment')->findOneById($envId);
            $target->setEnvironment($env);

            if(!$form->isValid()){

                $form1 =  array(
                    'formStep1' => $form->createView()
                );

                return $form1;
            }

            $db = $target->getMysqldb();

            if(empty($db)){
                // Step1

                switch($target->getEnvironment()->getType()){
                    case 'O':
                        $appRoot = $env->getHomedirsBase().'/'.$target->getUsername().'/vhosts/'.$app->getName();
                        $target->setApproot($appRoot);
                        $target->setPrimaryDomain($app->getName().'.'.$target->getUsername().'.'.$env->getHostname());
                        break;
                    case 'T':
                        $target->setPrimaryDomain($app->getName().'.'.$target->getUsername().'.'.$env->getHostname());
                    case 'A':
                        $target->setPrimaryDomain($app->getName().'.netvlies-demo.nl');
                    case 'P':
                        $target->setPrimaryDomain('www.'.$app->getName().'.nl');
                    default:
                        $appRoot = $env->getHomedirsBase().'/'.$app->getUsername().'/www/current';
                        $target->setApproot($appRoot);
                        break;
                }

                switch($app->getType()->getName()){
                    case 'symfony2':
                        $target->setWebroot($appRoot.'/web');
                        break;
                    default:
                        $target->setWebroot($appRoot);
                        break;
                }

                $target->setMysqldb($app->getName());
                $target->setMysqluser($app->getName());
                $target->setMysqlpw($app->getMysqlpw());
                $target->setLabel('Target settings for '.$target->getPrimaryDomain());

                // Reinitialize form with presetted data (secondPart), without binding to trigger PRE_SET event again,  so we have a plain form again ready for next submission
                $form = $this->createForm(new FormTargetType(), $target, array('app' => $target->getApplication(), 'envchoice'=>$envChoice, 'secondPart'=>true));
            }
            else{
                // Step2
                if($form->isValid()){
                    $em->persist($target);
                    $em->flush();
                    return $this->redirect($this->generateUrl('netvlies_publish_application_targets', array('id'=>$app->getId())));
                }
            }
        }

        return array(
            'formStep1' => $form->createView(),
        );

    }
}
