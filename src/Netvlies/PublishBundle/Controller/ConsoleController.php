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
use Netvlies\PublishBundle\Entity\ConsoleAction;

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
     * This action should never be called without having used the prepareCommand (which will prepare a log entry)
     *
     * @Route("/console/exec/{script}")
     * @Template()
     */
    public function execAction($script){
        $script = base64_decode($script);
        return array('script' => $script);
    }


    /**
     * Returns a simple key value array with all parameters needed for given target and revision
     * @todo It is probably not the best place to have this method in the controller (its also used in the getSettingsCommand). So move this to DIC
     * We can also loose the setContainer method in consoleAction!
     *
     * @param ConsoleAction $consoleAction
     * @return array
     */
    public function getParameters(ConsoleAction $consoleAction){

        $params = array();

        $target = $consoleAction->getTarget();
        $revision = $consoleAction->getRevision();
        $container = $consoleAction->getContainer();

        /**
         * @var \Netvlies\PublishBundle\Entity\Application $oApp
         */
         $app = $consoleAction->getApplication();

         /**
          * @var \Netvlies\PublishBundle\Entity\Environment $environment
          */
         $environment = $consoleAction->getEnvironment();

		// Entity attributes
        $params['project'] = $app->getName();
        $params['gitrepo'] = $app->getGitrepoSSH();
        $params['pubkeyfile'] = $container->getParameter('pubkeyfile');
        $params['privkeyfile'] = $container->getParameter('privkeyfile');
        $params['sudouser'] = $container->getParameter('sudouser');
        $params['revision'] = $revision;

        if(!is_null($target)){
            $params['username'] = $target->getUsername();
            $params['mysqldb'] = $target->getMysqldb();
            $params['mysqluser'] = $target->getMysqluser();
            $params['mysqlpw'] = $target->getMysqlpw();
            $params['webroot'] = $target->getWebroot();
            $params['approot'] = $target->getApproot();
            $params['caproot'] = $target->getCaproot();
        }

        if(!is_null($environment)){
            $params['homedirsBase'] = $environment->getHomedirsBase();
            $params['hostname'] = $environment->getHostname();
            $params['otap'] = $environment->getType();
            $params['bridgebin'] = $environment->getDeploybridgecommand();
        }

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

        return $params;
    }


    /**
     *
     * @param ConsoleAction $consoleAction
     * @return array
     * @throws \Exception
     * @Template()
     */
    public function prepareCommandAction(ConsoleAction $consoleAction){

        $command = $consoleAction->getCommand();
        $app = $consoleAction->getApplication();
        $container = $consoleAction->getContainer();

        if(is_null($command) || is_null($app) || is_null($container)){
            throw new \Exception('Console action is missing some required parameters (command|application|container)');
        }

        $target = $consoleAction->getTarget();
        $revision = $consoleAction->getRevision();

        /**
         * @var \Netvlies\PublishBundle\Services\GitBitbucket $gitService
         */
        $gitService = $this->get('git');
        $gitService->setApplication($app);
        $params = $this->getParameters($consoleAction);

        if(is_array($command)){
            $commands = $command;
        }
        else{
            $commands = array($command);
        }

        // Build script. Set CWD to local checkout of application
        $uid = md5(time().rand(0, 10000));
        $scriptBuilder = new ScriptBuilder($uid);

        // Change dir to local copy if exists
        $scriptBuilder->addLine('if [ -d "'.$gitService->getAbsolutePath().'" ]; then cd '.$gitService->getAbsolutePath().'; fi');

        foreach($commands as $command){
            // Parse command line options
            preg_match_all('/\${(.*?)}/', $command, $matches);

            foreach($matches[1] as $match){

                if(array_key_exists($match, $params)){
                    $command = str_replace('${'.$match.'}', $params[$match], $command);
                }
                elseif($match=='buildfile'){
                    $command = str_replace('${buildfile}', $gitService->getBuildFile(), $command);
                }
            }

            // Optionally build parameter bag (if ${params} is used in command)
            if(in_array('params', $matches[1])){

                $shellargs = array();


                if(strpos(trim($command), 'phing')===0){
                    // Phing execution
                    foreach($params as $key=>$value){
                        $shellargs[] = '-D'.$key.'='.escapeshellarg($value);
                        $capParams[] = '-S'.$key.'='.$value;
                    }
                    //@todo need to remove this as soon as capistrano targets are called natively instead of executing them through phing
                    $shellargs[] = '-Dcapparams'.escapeshellarg(implode(' ', $capParams));
                }
                elseif(strpos(trim($command), 'cap')===0){
                    // Capistrano execution
                    foreach($params as $key=>$value){
                        $shellargs[]='-S'.$key.'='.escapeshellarg($value);
                    }
                }
                else{
                    // Simple shell execution
                    throw new \Exception('Command type not yet implemented! use (phing|cap) '.$command);
                    //@todo set all params by using export
                }

                // Set params in command
                $command = str_replace('${params}', implode(' ', $shellargs), $command);
            }

            // Check if there are any unparsed parameters/options left in command
            $result = preg_match_all('/\${(.*?)}/', $command, $matches);
            if($result > 0){
                throw new \Exception('Couldnt translate some variables '.print_r($matches[1]));
            }

            $scriptBuilder->addLine($command);
        }

        // Prepare log entry
        $em  = $this->getDoctrine()->getEntityManager();
        $log = new DeploymentLog();
        $log->setCommand($command);
        $log->setDatetimeStart(new \DateTime());

        $user = array_key_exists('PHP_AUTH_USER', $_SERVER)? $_SERVER['PHP_AUTH_USER'] : 'nobody';
        $environment = $target->getEnvironment();
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
        $twigParams['command'] = $command;
        $twigParams['scriptpath'] = $scriptBuilder->getEncodedScriptPath();

        // We must redirect in order to make use of the apache proxy setting which path is fixed in httpd.conf
        // So therefore this method will just render a template where an iframe is within to control the real execution command
        return $twigParams;
    }
}