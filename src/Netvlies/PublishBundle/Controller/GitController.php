<?php
/**
 * @author: M. de Krijger
 * Creation date: 19-12-11
 */
namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\ApplicationRepository;
use Netvlies\PublishBundle\Entity\Application;
use Netvlies\PublishBundle\Entity\ConsoleLog;
use Netvlies\PublishBundle\Entity\ConsoleAction;
use Netvlies\PublishBundle\Form\FormApplicationType;


/**
 * @todo move this to bitbucket service
 */
class GitController extends Controller
{

    /**
     * @Route("/git/clone/{id}")
     * @param $id
     */
    public function cloneAction($id){
        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Application');

        /**
        * @var \Netvlies\PublishBundle\Entity\Application $oApp
        */
        $oApp = $oRepository->find($id);
        $sSiteRepository = $oApp->getAbsolutePath($this->container->getParameter('netvlies_publish.repositorypath'));

        $consoleAction = new ConsoleAction();
        $consoleAction->setCommand('git clone '.escapeshellarg($oApp->getScmURL()).' '.escapeshellarg($sSiteRepository));
        $consoleAction->setApplication($oApp);
        $consoleAction->setContainer($this->container);


        return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
            'consoleAction'  => $consoleAction
        ));
    }


    /**
 	 * @Route("/git/checkout/{id}/{reference}")
 	 */
     public function checkoutAction($id, $reference) {

         // Check if repo base path is there and writable
         $sPath = $this->container->getParameter('netvlies_publish.repositorypath');
         $oDir = new \SplFileInfo($sPath);

         if (!$oDir->isDir() || !$oDir->isWritable()) {
             throw new \Exception('Main repository directory does not exist or is not writable! (' . $sPath . ')');
         }

         $oEntityManager = $this->getDoctrine()->getEntityManager();
         $app = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->findOneById($id);
         $sSiteRepository = $app->getAbsolutePath($this->container->getParameter('netvlies_publish.repositorypath'));

         $commands = array(
             'cd '.escapeshellarg($sSiteRepository),
             'git fetch',
             'git checkout '.$reference
         );

         $consoleAction = new ConsoleAction();
         $consoleAction->setCommand($commands);
         $consoleAction->setApplication($app);
         $consoleAction->setContainer($this->container);

         // Get new console and execute git command
         return $this->forward('NetvliesPublishBundle:Console:prepareCommand', array(
             'consoleAction'  => $consoleAction
         ));
     }
}
