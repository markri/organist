<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\ApplicationRepository;
use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\ScriptBuilder;
use Netvlies\PublishBundle\Entity\DeploymentLog;
use Netvlies\PublishBundle\Form\FormApplicationType;



class ConsoleController extends Controller {


    /**
     * @todo currently not used, remove? Of make it suitable for e.g. a symfony console?
     * This route is fixed! Due to apache proxy setting that will redirect /console/open/anyterm to appropriate assets
     * @Route("/console/open/start")
     * @Template()

    public function openAction(){
        $workingDirectory='/var/www/vhosts/publish/web/repos/www.allaboutlease.nl';
        return array('workingdirectory'=>$workingDirectory);
    }
     * */

    /**
     * This route is fixed! Due to apache proxy setting that will redirect /console/exec/anyterm to appropriate assets
     * @Route("/console/exec/{script}")
     * @Template()
     */
    public function execAction($script){
        $script = base64_decode($script);

        // @todo conditionally add a log entry here
        // but since execTarget is the main purpose of this tool where all additional log params are saved it is kind of
        // useless. (we cant reach those extra params from here, we dont know which targetid is involved / or maybe there isnt a deployment id)
        // so maybe we can add a log entry here if it isnt already made (we can check db for uid), so every exec action is logged.

        return array('script' => $script);

    }


    /**
     * Returns a simple key value array with all parameters needed for given target and revision
     * @todo It is probably not the best place to have this method in the controller (its also used in the getSettingsCommand)
     *
     * @param $container
     * @param $target
     * @param $revision
     * @return array
     */
    public function getSettings($container, $target, $revision){

        $params = array();

        /**
         * @var \Netvlies\PublishBundle\Entity\Application $oApp
         */
         $app = $target->getApplication();

         /**
          * @var \Netvlies\PublishBundle\Entity\Environment $environment
          */
         $environment = $target->getEnvironment();

		// Entity attributes
        $params['project'] = $app->getName();
        $params['gitrepo'] = $app->getGitrepoSSH();
        $params['pubkeyfile'] = $container->getParameter('pubkeyfile');
        $params['privkeyfile'] = $container->getParameter('privkeyfile');
        $params['username'] = $target->getUsername();
        $params['mysqldb'] = $target->getMysqldb();
        $params['mysqluser'] = $target->getMysqluser();
        $params['mysqlpw'] = $target->getMysqlpw();
        $params['homedirsBase'] = $environment->getHomedirsBase();
        $params['sudouser'] = $container->getParameter('sudouser');
		$params['hostname'] = $environment->getHostname();
        $params['revision'] = $revision;
        $params['webroot'] = $target->getWebroot();
        $params['approot'] = $target->getApproot();
		$params['otap'] = $environment->getType();
        $params['bridgebin'] = $environment->getDeploybridgecommand();

		// user files and dirs
        $userfiles = $app->getUserFiles();
        $dirs = array();
        $files = array();
        foreach($userfiles as $file){
            $type = $file->getType();
            if($type=='D'){
                $dirs[] = $file->getPath();
            }
            else{
                $files[] = $file->getPath();
            }
        }
        $params['userfiles'] = implode(',', $files);
        $params['userdirs'] = implode(',', $dirs);


		// Also build a capistrano parameter bag from all previously given params
        $capParams = '';
        foreach($params as $key=>$value){
            $capParams[]='-S'.$key.'='.$value;
        }
        $params['capparams'] = implode(' ', $capParams);
        return $params;
    }



    /**
     * Is used for executing a phing target
     *
     * @Route("/console/target/{id}/{revision}")
     * @Route("/console/target/{id}"))
	 */
    public function execTargetAction($id, $revision=null){

        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Deployment');
        /**
         * @var \Netvlies\PublishBundle\Entity\Target $target
         */
        $target = $oRepository->findOneById($id);

       /**
        * @var \Netvlies\PublishBundle\Entity\Application $oApp
        */
        $oApp = $target->getApplication();
        $oApp->setBaseRepositoriesPath($this->container->getParameter('repositorypath'));

        /**
         * @var \Netvlies\PublishBundle\Entity\Environment $environment
         */
        $environment = $target->getEnvironment();

        // Get params
        $params = $this->getSettings($this->container, $target, $revision);
		$shellargs = array();
		
		foreach($params as $key=>$value){
			$shellargs[] = '-D'.$key.'='.escapeshellarg($value);
		}

		// build command
        $command = 'phing -f '.$oApp->getBuildFile().' '.$target->getPhingTarget()->getName().' '.implode(' ', $shellargs);
        $uid = md5(time().rand(0, 10000));
        $scriptBuilder = new ScriptBuilder($uid);
        $scriptBuilder->addLine($command);


        // Prepare log entry
        $em  = $this->getDoctrine()->getEntityManager();
        $log = new DeploymentLog();
        $log->setCommand($command);
        $log->setDatetimeStart(new \DateTime());


        $user = array_key_exists('PHP_AUTH_USER', $_SERVER)? $_SERVER['PHP_AUTH_USER'] : 'nobody';
        $log->setUser($user);
        $log->setDeploymentId($id);
        $log->setRevision($revision);
        $log->setHost($environment->getHostname());
        $log->setType($environment->getType());
        $log->setUid($uid);
        $em->persist($log);
        $em->flush();


        // We must redirect in order to make use of the apache proxy setting which path is fixed in httpd.conf
        return $this->redirect($this->generateUrl('netvlies_publish_console_exec', array('script'=>$scriptBuilder->getEncodedScriptPath())));
    }
}
