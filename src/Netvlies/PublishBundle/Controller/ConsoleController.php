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

use Netvlies\PublishBundle\Entity\ConsoleLog;
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
        if(!file_exists($script)){
            throw new \Exception('Whoops... You cant execute the same script again by just refreshing the page! :-)');
        }
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
        $params['gitrepo'] = $app->getScmURL();
        $params['repokey'] = $app->getScmKey();
        $params['initfiles'] = dirname(__DIR__).'/Resources/apptypes/'.$consoleAction->getApplicationType()->getName().'/files/.';
        $params['repositorypath'] = $container->getParameter('netvlies_publish.repositorypath');
        //$params['pubkeyfile'] = $container->getParameter('pubkeyfile');
        //$params['privkeyfile'] = $container->getParameter('privkeyfile');
        $params['sudouser'] = $container->getParameter('netvlies_publish.sudouser');
        $params['revision'] = $revision;

        if(!is_null($target)){
            $params['username'] = $target->getUsername();
            $params['mysqldb'] = $target->getMysqldb();
            $params['mysqluser'] = $target->getMysqluser();
            $params['mysqlpw'] = $target->getMysqlpw();
            $params['webroot'] = $target->getWebroot();
            $params['approot'] = $target->getApproot();
            $params['caproot'] = $target->getCaproot();
            $params['primarydomain'] = $target->getPrimaryDomain();
        }

        if(!is_null($environment)){
            $params['homedirsBase'] = $environment->getHomedirsBase();
            $params['hostname'] = $environment->getHostname();
            $params['otap'] = $environment->getType();
            $params['bridgebin'] = $environment->getDeploybridgecommand();
            $params['sshport'] = $environment->getSshPort();
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
     * @throws \Exception
     */
    public function prepareCommandAction(ConsoleAction $consoleAction){

        $command = $consoleAction->getCommand();
        $app = $consoleAction->getApplication();
        $container = $consoleAction->getContainer();

        if(is_null($command) || is_null($app) || is_null($container)){
            throw new \Exception('Console action is missing some required parameters (command|application|container)');
        }

        $revision = $consoleAction->getRevision();

        /**
         * @var \Netvlies\PublishBundle\Services\Scm\GitBitbucket $scmService
         */
        $scmService = $this->get($app->getScmService());

        $params = $this->getParameters($consoleAction);

        if(is_array($command)){
            $commands = $command;
        }
        else{
            $commands = array($command);
        }

        // Build script. Set CWD to local checkout of application
        $scriptBuilder = $this->get('scriptbuilder');
        $appPath = $app->getAbsolutePath($this->container->getParameter('netvlies_publish.repositorypath'));
        $keyfile = $scmService->getKeyfile();

        // Change dir to local copy if exists
        if(file_exists($appPath)){
            $scriptBuilder->addLine('cd '.$appPath);
        }

        // Set keyfile if present
        if(!empty($keyfile)){
            $scriptBuilder->addLine('eval `ssh-agent`');
            $scriptBuilder->addLine('`ssh-add '.$keyfile.'`');
        }


        foreach($commands as $command){
            // Parse command line options
            preg_match_all('/\${(.*?)}/', $command, $matches);

            foreach($matches[1] as $match){

                if(array_key_exists($match, $params)){
                    $command = str_replace('${'.$match.'}', $params[$match], $command);
                }
                elseif($match=='buildfile'){
                    $command = str_replace('${buildfile}', $appPath.'/build.xml', $command);
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
                }
                elseif(strpos(trim($command), 'cap')===0){
                    // Capistrano execution
                    foreach($params as $key=>$value){
                        $shellargs[]='-S'.$key.'='.escapeshellarg($value);
                    }
                }
                else{
                    // Simple shell execution
                    //@todo set all params by using export
                    throw new \Exception('Command type not yet implemented! use (phing|cap) '.$command);
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

        if(!empty($keyfile)){
            $scriptBuilder->addLine('ssh-agent -k > /dev/null 2>&1');
            $scriptBuilder->addLine('unset SSH_AGENT_PID');
            $scriptBuilder->addLine('unset SSH_AUTH_SOCK');
        }

        // Prepare log entry
        $em  = $this->getDoctrine()->getEntityManager();
        $user = array_key_exists('PHP_AUTH_USER', $_SERVER)? $_SERVER['PHP_AUTH_USER'] : 'nobody';
        $environment = $consoleAction->getEnvironment();
        $target = $consoleAction->getTarget();
        $hostname = isset($environment)?$environment->getHostname():'';
        $type = isset($environment)?$environment->getType():'';
        $targetId = isset($target)?$consoleAction->getTarget()->getId():'';

        $log = new ConsoleLog();
        $log->setCommand(implode('; ', $commands));
        $log->setDatetimeStart(new \DateTime());
        $log->setUser($user);
        $log->setTargetId($targetId);
        $log->setRevision($revision);
        $log->setHost($hostname);
        $log->setType($type);
        $log->setUid($uid);

        $em->persist($log);
        $em->flush();

        // We must redirect in order to prevent reposting the same command.
        return $this->redirect($this->generateUrl('netvlies_publish_console_executecommand', array(
            'id'=>$app->getId(),
            'command'=>implode('; ', $commands),
            'scriptpath'=>base64_encode($scriptBuilder->getScriptPath())
        )));
    }


    /**
     * @Route("/console/frame/exec/{id}/{scriptpath}/{command}")
     * @Template()
     */
    public function executeCommandAction($id, $command, $scriptpath){
        // We must redirect in order to make use of the apache proxy setting which path is fixed in httpd.conf
        // So therefore this method will just render a template where an iframe is loaded with an anyterm console where the command is executed
        // The script (encoded scriptpath) will be selfdestructed at the end, so re-executing is impossible by then
        $twigParams = array();
        $em  = $this->getDoctrine()->getEntityManager();
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        $twigParams['application'] = $app;
        $twigParams['command'] = $command;
        $twigParams['scriptpath'] = $scriptpath;

        return $twigParams;
    }


}