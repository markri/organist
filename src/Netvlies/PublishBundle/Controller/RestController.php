<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Form\RestApplicationType;
use Netvlies\PublishBundle\Form\RestEnvironmentType;
use Netvlies\PublishBundle\Form\RestDeploymentType;
use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\Environment;
use Netvlies\PublishBundle\Entity\Deployment;


class RestController extends Controller
{

    /**
     * @Route("/rest/application", requirements={"_method"="post"})
     */
    public function createApplicationAction()
    {
        $app = new Application();
        $form = $this->createForm(new RestApplicationType(), $app);
        $request = $this->getRequest();
        $response = new Response();

        $post = $request->request->all();
        $form->bind($post);

        /**
         * ConstraintViolationList $errors
         */
        $errors = $this->get('validator')->validate($app);

        if ($errors->count() == 0) {
            $em = $this->getDoctrine()->getEntityManager();
            $apps = $em->getRepository('NetvliesPublishBundle:Application')->findByName($app->getName());


            if(count($apps)>0){
                $newapp = $apps[0];
                $newapp->setCustomer($app->getCustomer());
                $app = $newapp;
            }

            $em->persist($app);
            $em->flush();
        }
        else {
            $response->setStatusCode(400);
        }

        $content = $this->renderView('NetvliesPublishBundle:Rest:createApplication.html.twig', array('errors' => $errors));
        $response->setContent($content);

        return $response;

    }


    /**
     * @Route("/rest/environment", requirements={"_method"="post"})
     */
    public function createEnvironment()
    {
        $environment = new Environment();
        $form = $this->createForm(new RestEnvironmentType(), $environment);
        $request = $this->getRequest();
        $response = new Response();

        $post = $request->request->all();
        $form->bind($post);

        /**
         * ConstraintViolationList $errors
         */
        $errors = $this->get('validator')->validate($environment);

        if ($errors->count() == 0) {


            $em = $this->getDoctrine()->getEntityManager();

            $envs1 = $em->getRepository('NetvliesPublishBundle:Environment')->getByTypeAndHost($environment->getType(), $environment->getHostname());
            $envs2 = $em->getRepository('NetvliesPublishBundle:Environment')->findByKeyname($environment->getKeyname());
            $envs = array_merge($envs1, $envs2);

            if(count($envs) == 0){
                $em->persist($environment);
                $em->flush();
            }
            else{
                $violation = new ConstraintViolation('Given parameters are not unique, please give unique combination of (host and type) and a unique keyname', array(), null, null, null);
                $errors->add($violation);
            }
        }
        else {
            $response->setStatusCode(400);
        }

        $content = $this->renderView('NetvliesPublishBundle:Rest:createEnvironment.html.twig', array('errors' => $errors));
        $response->setContent($content);

        return $response;
    }

    /**
     * @Route("/rest/deployment", requirements={"_method"="post"})
     */
    public function createDeployment()
    {
        $deployment = new Deployment();
        $form = $this->createForm(new RestDeploymentType(), $deployment, array('entitymanager'=>$this->getDoctrine()->getEntityManager()));
        $request = $this->getRequest();
        $response = new Response();

        $post = $request->request->all();
        $form->bind($post);

        /**
         * ConstraintViolationList $errors
         */
        $errors = $this->get('validator')->validate($deployment);

        if ($errors->count() == 0) {

            $em = $this->getDoctrine()->getEntityManager();
            $deployments = $em->getRepository('NetvliesPublishBundle:Deployment')->getByAppAndEnv($deployment->getApplication(), $deployment->getEnvironment());

            if(count($deployments) == 0){
                $em->persist($deployment);
                $em->flush();
            }
            else{
                echo 'deployment already exists';
                exit;
                $violation = new ConstraintViolation('Given parameters are not unique, please give unique combination of (host and type) and a unique keyname', array(), null, null, null);
                $errors->add($violation);
            }
        }
        else {
            $response->setStatusCode(400);
        }

        $content = $this->renderView('NetvliesPublishBundle:Rest:createDeployment.html.twig', array('errors' => $errors));
        $response->setContent($content);

        return $response;
    }




}

