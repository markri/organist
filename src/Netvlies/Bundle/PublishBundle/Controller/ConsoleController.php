<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\Bundle\PublishBundle\Entity\ApplicationRepository;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\Deployment;
use Netvlies\Bundle\PublishBundle\Entity\Rollback;

use Netvlies\Bundle\PublishBundle\Entity\ConsoleLog;
use Netvlies\Bundle\PublishBundle\Entity\ConsoleAction;

use Netvlies\Bundle\PublishBundle\Form\FormApplicationType;



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
     * @todo this should be moved into DIC into a twig extension
     * @Route("/console/frame/exec/{id}/{scriptpath}/{command}")
     * @Template()
     */
    public function executeCommandAction($id, $command, $scriptpath){
        // We must redirect in order to make use of the apache proxy setting which path is fixed in httpd.conf
        // So therefore this method will just render a template where an iframe is loaded with an anyterm console where the command is executed
        // The script (encoded scriptpath) will be selfdestructed at the end, so re-executing is impossible by then
        $twigParams = array();
        $em  = $this->getDoctrine()->getManager();
        $app = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        $twigParams['application'] = $app;
        $twigParams['command'] = $command;
        $twigParams['scriptpath'] = $scriptpath;

        return $twigParams;
    }


}