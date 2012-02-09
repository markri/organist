<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\Target;
use Netvlies\PublishBundle\Entity\ConsoleAction;
use Netvlies\PublishBundle\Form\FormTargetEditType;
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

        return $this->handleFormStep1($app);
    }


    /**
     * @Route("/application/{id}/target/new/step2")
     * @Template()
     */
    public function createStep2Action($id){

        $em  = $this->getDoctrine()->getEntityManager();
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        return $this->handleFormStep2($app);
    }


    /**
     * @Route("/target/edit/{id}")
     * @Template()
     */
    public function editAction($id){
        $em  = $this->getDoctrine()->getEntityManager();
        $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($id);
        $request = $this->getRequest();

        $form = $this->createForm(new FormTargetEditType(), $target, array());

        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);
            if($form->isValid()){
                $em->persist($target);
                $em->flush($target);

                return $this->redirect($this->generateUrl('netvlies_publish_application_targets', array('id'=>$target->getApplication()->getId())));
            }
        }

        return array(
            'application' => $target->getApplication(),
            'form' => $form->createView(),
        );

    }


    /**
     *
     * @Route("/target/delete/{id}")
     * @todo add confirmation
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
     * @todo validation is now done by HTML5 required attributes, which is ok, but may fail when relying on SSP validation, we dont have validation groups2
     * @param $app \Netvlies\PublishBundle\Entity\Application
     * @return array
     */
    protected function handleFormStep1($app){

		$em  = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $target = new Target();
        $target->setApplication($app);

		$envChoice = new EnvironmentsType($em);
        $formStep1 = $this->createForm(new FormTargetStep1Type(), $target, array('envchoice'=>$envChoice));

        if($request->getMethod() == 'POST'){

            $formStep1->bindRequest($request);

            // This is still an id, because we use a choicelist in order to get an ordered list of envs by O, T, A, P
            $envId = $target->getEnvironment();

            if($formStep1->isValid()){

                $request->getSession()->set('target.env', $envId);
                $request->getSession()->set('target.user', $target->getUsername());

                return $this->redirect($this->generateUrl('netvlies_publish_target_createstep2', array('id'=>$app->getId())));
            }
        }

        return array(
            'application' => $app,
            'form' => $formStep1->createView(),
        );

    }


    protected function handleFormStep2($app)
    {

        $target = new Target();
        $request = $this->getRequest();
        $em  = $this->getDoctrine()->getEntityManager();

        $envId = $request->getSession()->get('target.env');
        $user = $request->getSession()->get('target.user');

        /**
         * @var \Netvlies\PublishBundle\Entity\Environment $env
         */
        $env = $em->getRepository('NetvliesPublishBundle:Environment')->findOneById($envId);
        $target->setApplication($app);
        $target->setEnvironment($env);
        $target->setUsername($user);

        if($request->getMethod() != 'POST'){
            // Skip this part if not needed

            // Init default values in target
            switch($env->getType()){
                case 'O':
                    $appRoot = $env->getHomedirsBase().'/'.$target->getUsername().'/vhosts/'.$app->getName();
                    $target->setApproot($appRoot);
                    $target->setPrimaryDomain($app->getName().'.'.$target->getUsername().'.'.$env->getHostname());
                    break;
                case 'T':
                    if($target->getUsername()=='tester'){
                        $target->setPrimaryDomain($app->getName().'.'.$env->getHostname());
                    }
                    else{
                        $target->setPrimaryDomain($app->getName().'.'.$target->getUsername().'.'.$env->getHostname());
                    }

                    $appRoot = $env->getHomedirsBase().'/'.$target->getUsername().'/vhosts/'.$app->getName().'/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($env->getHomedirsBase().'/'.$target->getUsername().'/vhosts/'.$app->getName());
                    break;
                case 'A':
                    $target->setPrimaryDomain($app->getName().'.netvlies-demo.nl');
                    $appRoot = $env->getHomedirsBase().'/'.$target->getUsername().'/www/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($env->getHomedirsBase().'/'.$target->getUsername().'/www');
                    break;
                case 'P':
                    $target->setPrimaryDomain('www.'.$app->getName().'.nl');
                    $appRoot = $env->getHomedirsBase().'/'.$target->getUsername().'/www/current';
                    $target->setApproot($appRoot);
                    $target->setCaproot($env->getHomedirsBase().'/'.$target->getUsername().'/www');
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
            $target->setLabel('('.$env->getType().') '.$target->getPrimaryDomain());
        }

        $formStep2 = $this->createForm(new FormTargetStep2Type(), $target, array());
        $em  = $this->getDoctrine()->getEntityManager();

        if($request->getMethod() == 'POST'){

            // Init form2
            $formStep2->bindRequest($request);

            if($formStep2->isValid()){
                $em->persist($target);
                $em->flush($target);

                if($target->getEnvironment()->getType()=='O'){
                    return $this->redirect($this->generateUrl('netvlies_publish_application_targets', array('id'=>$app->getId())));
                }

                $command = $target->getApplication()->getType()->getSetupTAPCommand();
                $consoleAction = new ConsoleAction();
                $consoleAction->setTarget($target);
                $consoleAction->setCommand($command);
                $consoleAction->setContainer($this->container);

                return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
                    'consoleAction'  => $consoleAction
                ));
            }
        }

        return array(
            'application' => $app,
            'form' => $formStep2->createView(),
        );
    }



    /**
     * Used within AJAX call
     *@Route("/target/getReference")
     */
    public function getReferenceAction(){

        // and description of current reference
        $id = $this->get('request')->query->get('id');

        $em  = $this->getDoctrine()->getEntityManager();
        $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($id);
        $app = $target->getApplication();

        $gitService = $this->get('git');
        $gitService->setApplication($app);
        $remoteBranches = $gitService->getRemoteBranches();

        /**
         * @var \Netvlies\PublishBundle\Entity\Target $target
         */
        $branch = $target->getCurrentBranch();

        $oldRef = $target->getCurrentRevision();
        $newRef = array_search($branch, $remoteBranches);

        return $this->handleChangesetsRendering($gitService, $oldRef, $newRef);

    }

    /**
     * Used within AJAX call
     *@Route("/target/loadChangeset")
     */
    public function loadChangesetAction(){
        // and description of current reference
        $id = $this->get('request')->query->get('id');

        $em  = $this->getDoctrine()->getEntityManager();
        $target = $em->getRepository('NetvliesPublishBundle:Target')->findOneById($id);
        if(is_null($target)){
            throw new \Exception('couldnt find target by id '.$id);
        }
        $app = $target->getApplication();

        $gitService = $this->get('git');
        $gitService->setApplication($app);

        $oldRef = $target->getCurrentRevision();
        $newRef = $this->get('request')->query->get('ref');

        return $this->handleChangesetsRendering($gitService, $oldRef, $newRef);
    }



    protected function handleChangesetsRendering($gitService, $oldRef, $newRef){
        $changesets = $gitService->getLastChangesets($newRef);

        if(empty($newRef) && count($changesets)>0){
            $newRef = $changesets[0]['raw_node'];
            $oldRef = '.. (first deployment)';
        }

        $messages = array();
        $foundAll = false;

        foreach($changesets as $changeset){
            $messages[] = array(
                'author' => $changeset['author'],
                'message' => $changeset['message']
            );
            if($changeset['raw_node']==$oldRef){
                $foundAll = true;
            }
        }

        $params = array();
        $params['foundall'] = $foundAll;
        $params['messages'] = $messages;
        $params['oldref'] = $oldRef;
        $params['newref'] = $newRef;
        $params['bburl'] = $gitService->getBitbucketChangesetURL();

        return $this->render('NetvliesPublishBundle:Target:changeset.html.twig', $params);
    }

}
