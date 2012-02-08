<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\ApplicationRepository;
use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\Deployment;
use Netvlies\PublishBundle\Entity\Rollback;

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
        $params['caproot'] = $target->getCaproot();
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
     * @todo Deployment should have an interface so we can make a generic method for every kind of type
     * No route is it is internally redirected
     *
     * @param Deployment $deployment
     * @Template()
     */
    public function deployAction(Deployment $deployment){

        $command = $deployment->getTarget()->getApplication()->getType()->getDeployCommand();
        $app = $deployment->getTarget()->getApplication();
        $container = $this->container;
        $target = $deployment->getTarget();
        $environment = $deployment->getTarget()->getEnvironment();
        $revision = $deployment->getReference();

        return $this->translateCommand($command, $app, $container, $target, $environment, $revision);

    }

    /**
     * @todo Rollback should have an interface so we can make a generic method for every kind of type
     * No route; is it is internally redirected
     *
     * @param Rollback $rollback
     * @Template()
     */
    public function rollBackAction(Rollback $rollback){

        $command = $rollback->getTarget()->getApplication()->getType()->getRollbackCommand();
        $app = $rollback->getTarget()->getApplication();
        $container = $this->container;
        $target = $rollback->getTarget();
        $environment = $rollback->getTarget()->getEnvironment();

        return $this->translateCommand($command, $app, $container, $target, $environment);
    }



    protected function translateCommand($command, $app, $container, $target, $environment, $revision=null){
        /**
         * @var \Netvlies\PublishBundle\Services\GitBitbucket $gitService
         */
        $gitService = $this->get('git');
        $gitService->setApplication($app);

        preg_match_all('/\${(.*?)}/', $command, $matches);

        foreach($matches[1] as $match){
            switch($match){
                case 'project':
                    $command = str_replace('${project}', $app->getName(), $command);
                    break;
                case 'gitrepo':
                    $command = str_replace('${gitrepo}', $app->getGitrepoSSH(), $command);
                    break;
                case 'pubkeyfile':
                    $command = str_replace('${pubkeyfile}', $container->getParameter('pubkeyfile'), $command);
                    break;
                case 'privkeyfile':
                    $command = str_replace('${privkeyfile}', $container->getParameter('privkeyfile'), $command);
                    break;
                case 'username':
                    $command = str_replace('${username}', $target->getUsername(), $command);
                    break;
                case 'mysqldb':
                    $command = str_replace('${mysqldb}', $target->getMysqldb(), $command);
                    break;
                case 'mysqluser':
                    $command = str_replace('${mysqluser}', $target->getMysqluser(), $command);
                    break;
                case 'mysqlpw':
                    $command = str_replace('${mysqlpw}', $target->getMysqlpw(), $command);
                    break;
                case 'homedirsBase':
                    $command = str_replace('${homedirsBase}', $environment->getHomedirsBase(), $command);
                    break;
                case 'sudouser':
                    $command = str_replace('${sudouser}', $container->getParameter('sudouser'), $command);
                    break;
                case 'hostname':
                    $command = str_replace('${hostname}', $environment->getHostname(), $command);
                    break;
                case 'revision':
                    $command = str_replace('${revision}', $revision, $command);
                    break;
                case 'webroot':
                    $command = str_replace('${webroot}', $target->getWebroot(), $command);
                    break;
                case 'approot':
                    $command = str_replace('${approot}', $target->getApproot(), $command);
                    break;
                case 'caproot':
                    $command = str_replace('${caproot}', $target->getCaproot(), $command);
                    break;
                case 'otap':
                    $command = str_replace('${otap}', $environment->getType(), $command);
                    break;
                case 'bridgebin':
                    $command = str_replace('${bridgebin}', $environment->getDeploybridgecommand(), $command);
                    break;
                case 'buildfile':
                    $command = str_replace('${buildfile}', $gitService->getBuildFile(), $command);
                    break;
                default:
                    break;
            }
        }

        // Get additional params
        $params = $this->getSettings($this->container, $target, $revision);
		$shellargs = array();

		foreach($params as $key=>$value){
			$shellargs[] = '-D'.$key.'='.escapeshellarg($value);
		}

        $command = str_replace('${params}', implode(' ', $shellargs), $command);

        $result = preg_match_all('/\${(.*?)}/', $command, $matches);
        if($result > 0){
            throw new \Exception('Couldnt translate some variables '.print_r($matches[1]));
        }

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
        $log->setTargetId($target->getId());
        $log->setRevision($revision);
        $log->setHost($environment->getHostname());
        $log->setType($environment->getType());
        $log->setUid($uid);
        $log->setRevision('');
        $em->persist($log);
        $em->flush();

        $twigParams = array();
        $twigParams['application'] = $app;
        $twigParams['targetid'] = $target->getId();
        $twigParams['revision'] = $revision;
        $twigParams['scriptpath'] = $scriptBuilder->getEncodedScriptPath();

        // We must redirect in order to make use of the apache proxy setting which path is fixed in httpd.conf
        // So therefore this method will just render a template where an iframe is within to control the real execution command
        return $twigParams;
    }
//
//
//    public function copyContentAction(CopyContent $copyContent){
//
//    }
//

//    public function customTargetAction(CustomTarget $customTarget){
//          // type phing|capistrano must be in CustomTarget
            // extra params can be given into extra form from which entity must be in CustomTarget
            //
//    }
//    public function setupDevAction(SetupDev $setupDev){
//
//}






//    /**
//     * Is used for executing a phing target
//     *
//     * @Route("/console/target/{id}/{revision}")
//     * @Route("/console/target/{id}"))
//	 */
//    protected function execTargetAction($id, $revision=null){
//
//        $oEntityManager = $this->getDoctrine()->getEntityManager();
//        $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Target');
//        /**
//         * @var \Netvlies\PublishBundle\Entity\Target $target
//         */
//        $target = $oRepository->findOneById($id);
//
//       /**
//        * @var \Netvlies\PublishBundle\Entity\Application $oApp
//        */
//        $oApp = $target->getApplication();
//
//        /**
//         * @var \Netvlies\PublishBundle\Services\GitBitbucket $gitService
//         */
//        $gitService = $this->get('git');
//        $gitService->setApplication($oApp);
//
//        /**
//         * @var \Netvlies\PublishBundle\Entity\Environment $environment
//         */
//        $environment = $target->getEnvironment();
//
//        // Get params
//        $params = $this->getSettings($this->container, $target, $revision);
//		$shellargs = array();
//
//		foreach($params as $key=>$value){
//			$shellargs[] = '-D'.$key.'='.escapeshellarg($value);
//		}
//
//		// build command
//        $oApp->getType()->getDeployCommand();
//
//        // @todo this will break. Because of deploy which is static
//        $command = 'phing -f '.$gitService->getBuildFile().' deploy '.implode(' ', $shellargs);
//        $uid = md5(time().rand(0, 10000));
//        $scriptBuilder = new ScriptBuilder($uid);
//        $scriptBuilder->addLine($command);
//
//
//        // Prepare log entry
//        $em  = $this->getDoctrine()->getEntityManager();
//        $log = new DeploymentLog();
//        $log->setCommand($command);
//        $log->setDatetimeStart(new \DateTime());
//
//
//        $user = array_key_exists('PHP_AUTH_USER', $_SERVER)? $_SERVER['PHP_AUTH_USER'] : 'nobody';
//        $log->setUser($user);
//        $log->setDeploymentId($id);
//        $log->setRevision($revision);
//        $log->setHost($environment->getHostname());
//        $log->setType($environment->getType());
//        $log->setUid($uid);
//        $em->persist($log);
//        $em->flush();
//
//
//        // We must redirect in order to make use of the apache proxy setting which path is fixed in httpd.conf
//        return $this->redirect($this->generateUrl('netvlies_publish_console_exec', array('script'=>$scriptBuilder->getEncodedScriptPath())));
//    }
}