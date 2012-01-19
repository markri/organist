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
        // useless. (we cant reach those extra params from here, we dont know which deploymentid is involved / or maybe there isnt a deployment id)
        // so maybe we can add a log entry here if it isnt already made (we can check db for uid), so every exec action is logged.

        return array('script' => $script);

    }


    /**
     * Is used for executing a phing target
     *
     * @Route("/console/deployment/{id}/{revision}")
     * @Route("/console/deployment/{id}"))
	 */
    public function execTargetAction($id, $revision=null){

        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Deployment');
        /**
         * @var \Netvlies\PublishBundle\Entity\Deployment $deployment
         */
        $deployment = $oRepository->findOneById($id);

       /**
        * @var \Netvlies\PublishBundle\Entity\Application $oApp
        */
        $oApp = $deployment->getApplication();
        $oApp->setBaseRepositoriesPath($this->container->getParameter('repositorypath'));

        /**
         * @var \Netvlies\PublishBundle\Entity\Environment $environment
         */
        $environment = $deployment->getEnvironment();
        $params = array();

		// Entity attributes
        $params[] = '-Dproject='.$oApp->getName();
        $params[] = '-Dgitrepo='.escapeshellarg($oApp->getGitrepo());
        $params[] = '-Dpubkeyfile='.$this->container->getParameter('pubkeyfile');
        $params[] = '-Dprivkeyfile='.$this->container->getParameter('privkeyfile');
        $params[] = '-Dusername='.$deployment->getUsername();
        $params[] = '-Dmysqldb='.$deployment->getMysqldb();
        $params[] = '-Dmysqluser='.$deployment->getMysqluser();
        $params[] = '-Dmysqlpw='.$deployment->getMysqlpw();
        $params[] = '-DhomedirsBase='.$environment->getHomedirsBase();
        $params[] = '-Dsudouser='.$this->container->getParameter('sudouser');
		$params[] = '-Dhostname='.$environment->getHostname();
        $params[] = '-Drevision='.$revision;
        $params[] = '-Dwebroot='.$deployment->getWebroot();
        $params[] = '-Dapproot='.$deployment->getApproot();
		$params[] = '-Dotap='.$environment->getType();
        $params[] = '-Dbridgebin='.$environment->getDeploybridgecommand();

		// user files and dirs
        $userfiles = $oApp->getUserFiles();
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
        $params[] = '-Duserfiles='.escapeshellarg(implode(',', $files));
        $params[] = '-Duserdirs='.escapeshellarg(implode(',', $dirs));


		// Also build a capistrano parameter bag from all previously given params
        $capParams = '';
        foreach($params as $param){
            $capParams[]=preg_replace('/^-D/', '-S', $param, 1);
        }
        $params[] = '-Dcapparams='.escapeshellarg(implode(' ', $capParams));


		// build command
        $command = 'phing -f '.$oApp->getBuildFile().' '.$deployment->getPhingTarget()->getName().' '.implode(' ', $params);
        $uid = md5(time().rand(0, 10000));
        $scriptBuilder = new ScriptBuilder($uid);
        $scriptBuilder->addLine($command);


        // Prepare log entry
        $em  = $this->getDoctrine()->getEntityManager();
        $log = new DeploymentLog();
        $log->setCommand($command);
        $log->setDatetimeStart(new \DateTime());

        $log->setUser($_SERVER['PHP_AUTH_USER']);
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
