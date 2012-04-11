<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\ApplicationRepository;
use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\ConsoleAction;
use Netvlies\PublishBundle\Entity\UserFiles;

use Netvlies\PublishBundle\Form\FormApplicationEditType;
use Netvlies\PublishBundle\Form\FormApplicationEnrichType;
use Netvlies\PublishBundle\Form\FormApplicationDeployType;
use Netvlies\PublishBundle\Form\FormApplicationRollbackType;
use Netvlies\PublishBundle\Form\FormApplicationDeployOType;
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
        $repokey = $app->getScmKey();
        if(empty($repokey)){
            return $this->redirect($this->generateUrl('netvlies_publish_application_enrich', array('id'=>$app->getId())));
        }

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
        $repokey = $app->getScmKey();
        if(empty($repokey)){
            return $this->redirect($this->generateUrl('netvlies_publish_application_enrich', array('id'=>$app->getId())));
        }

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
        $repokey = $app->getScmKey();

        if(empty($repokey)){
            return $this->redirect($this->generateUrl('netvlies_publish_application_enrich', array('id'=>$app->getId())));
        }

        $scmService = $this->get($app->getScmService());
        $remoteBranches = $scmService->getBranches($app);

        $form = $this->createForm(new FormApplicationEditType(), $app, array('branchchoice' => new BranchesType($remoteBranches)));
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){
                //$this->getRequest()->getSession()->remove('remoteBranches');
                $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$id)));
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
        /**
         * @var \Netvlies\PublishBundle\Entity\Application $app
         */
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        // Copied chars array from current password generator
        $chars = '345678934567893456789abcdefghjkmnpqrtuvwxyzABCDEFGHJKLMNPQRTUVWXY';
        $passwd = '';
        for($i = 0; $i < 10; $i++) {
            $passwd .= substr($chars, rand(0, strlen($chars)), 1);
        }

        $app->setMysqlpw($passwd);
        $app->setScmKey($app->getName());

        $scmService = $this->get($app->getScmService());
        $app->setScmURL($scmService->getScmURL($app));

        $form = $this->createForm(new FormApplicationEnrichType(), $app);
        $request = $this->getRequest();


        if($request->getMethod() == 'POST'){

            $form->bindRequest($request);

            if($form->isValid()){

                $em->persist($app);
                $em->flush();

                if($app->getType()->getName()=='symfony2'){
                    // Add vendor as shared directory for symfony2
                    $userFile = new UserFiles();
                    $userFile->setApplication($app);
                    $userFile->setPath('vendor');
                    $userFile->setType('D');

                    $em->persist($userFile);
                    $em->flush();
                }

                $repoExists = $scmService->existRepo($app);
                $pathExists = false;

                $appPath = $app->getAbsolutePath($this->container->getParameter('netvlies_publish.repositorypath'));
                if(file_exists($appPath)){
                    // We're finished. Because directory already exists. Which means that it is already cloned
                    // We're not going to change existing checkout
                    $pathExists = true;
                }

                if($repoExists && $pathExists){
                    // just exit
                    return $this->redirect($this->generateUrl('netvlies_publish_application_view', array('id'=>$app->getId())));
                }
                elseif($repoExists && !$pathExists){
                    // just path doesnt exist. So just clone the app and redirect
                    return $this->forward('NetvliesPublishBundle:Git:clone', array(
                        'id'  => $app->getId()
                    ));
                }
                elseif(!$repoExists && $pathExists){
                    // huh? How is this possible?
                    throw new \Exception('Repo doesnt exist but path does??? That cant be true (hopefully)!');
                }

                // Repo and local clone doesnt exist
                $result = $scmService->createRepository();
				if(!$result){
					echo 'Couldnt create repository. Connection failed';
					exit;
				}

                $scriptPath = $app->getType()->getInitScriptPath();
                if(!file_exists($scriptPath)){
                    // Nothing to do, so just clone app into repo path
                    return $this->forward('NetvliesPublishBundle:Git:clone', array(
                        'id'  => $app->getId()
                    ));
                }

                // Fetch init script and assign to consoleaction
                $lines = file($scriptPath, FILE_IGNORE_NEW_LINES);
                $action = new ConsoleAction();
                $action->setCommand($lines);
                $action->setApplication($app);
                $action->setContainer($this->container);

                // Execute currently build consoleAction
                return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
                    'consoleAction'  => $action
                ));
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
        $repokey = $app->getScmKey();
        if(empty($repokey)){
            return $this->redirect($this->generateUrl('netvlies_publish_application_enrich', array('id'=>$app->getId())));
        }

        $scmService = $this->get($app->getScmService());
        $branches = $scmService->getBranches($app);
        $consoleAction = new ConsoleAction();
        $consoleAction->setContainer($this->container);
        $consoleAction->setApplication($app);

        $deployForm = $this->createForm(new FormApplicationDeployType(), $consoleAction, array('branchchoice' => new BranchesType($branches), 'app'=>$app));
        $rollbackForm = $this->createForm(new FormApplicationRollbackType(), $consoleAction, array('app'=>$app));
        $deployOForm = $this->createForm(new FormApplicationDeployOType(), $consoleAction, array('branchchoice' => new BranchesType($branches), 'app'=>$app));
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){

            if ($request->request->has($deployForm->getName())){
                $deployForm->bindRequest($request);
                $consoleAction->setCommand($app->getType()->getDeployCommand());

                if($deployForm->isValid()){

                    if(!array_key_exists($consoleAction->getRevision(), $branches)){
                        throw new \Exception('Whoops somebody just updated the git repository between previous dashboard load and now');
                    }

                    $target = $consoleAction->getTarget();
                    $target->setCurrentBranch($branches[$consoleAction->getRevision()]);
                    $target->setCurrentRevision($consoleAction->getRevision());
                    $em->persist($target);
                    $em->flush();

                    return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
                        'consoleAction'  => $consoleAction
                    ));
                }
            }

            if ($request->request->has($rollbackForm->getName())){
                $rollbackForm->bindRequest($request);
                $consoleAction->setCommand($app->getType()->getRollbackCommand());
                if($rollbackForm->isValid()){
                    return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
                        'consoleAction'  => $consoleAction
                    ));
                }
            }


            if ($request->request->has($deployOForm->getName())){
                $deployOForm->bindRequest($request);
                $consoleAction->setCommand($app->getType()->getDeployOCommand());
                if($deployOForm->isValid()){
                    return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
                        'consoleAction'  => $consoleAction
                    ));
                }
            }
        }

        return array(
            'deployForm' => $deployForm->createView(),
            'rollbackForm' => $rollbackForm->createView(),
            'deployOForm' => $deployOForm->createView(),
            'application' => $app,
        );
    }

}
