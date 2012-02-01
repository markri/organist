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
use Netvlies\PublishBundle\Entity\ScriptBuilder;
use Netvlies\PublishBundle\Entity\DeploymentLog;
use Netvlies\PublishBundle\Form\FormApplicationType;



class GitController extends Controller
{

    /**
     * @Route("/git/clone/{id}")
     * @Template()
     * @param $id
     */
    public function cloneAction($id){
         $oEntityManager = $this->getDoctrine()->getEntityManager();
         $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Application');


        /**
         * @var \Netvlies\PublishBundle\Entity\Application $oApp
         */
         $oApp = $oRepository->find($id);
         $oApp->setBranchToFollow('master');

        $oEntityManager->persist($oApp);
        $oEntityManager->flush();

        /**
         * @var \Netvlies\PublishBundle\Services\GitBitbucket $gitService
         */
        $gitService = $this->get('git');
        $gitService->setApplication($oApp);
        $sSiteRepository = $gitService->getAbsolutePath();

         $scriptBuilder = new ScriptBuilder(time());
         //$scriptBuilder->addLine('rm -rf '.escapeshellarg($oApp->getAbsolutePath()));
         $scriptBuilder->addLine('git clone '.escapeshellarg($oApp->getGitrepoSSH()).' '.escapeshellarg($sSiteRepository));

        //@todo add log entry

        return array('scriptpath' => $scriptBuilder->getEncodedScriptPath(),
                     'application' => $oApp);
    }



//    /**
// 	 * @Route("/git/fetch/{id}")
//     * @Template()
// 	 */
//     public function fetchAction($id) {
//
//         // Check if repo base path is there and writable
//         $sPath = $this->container->getParameter('repositorypath');
//         $oDir = new \SplFileInfo($sPath);
//
//         if (!$oDir->isDir() || !$oDir->isWritable()) {
//             throw new Exception('Main repository directory does not exist or is not writable! (' . $sPath . ')');
//         }
//
//         $oEntityManager = $this->getDoctrine()->getEntityManager();
//
//         /**
//          * @var \Netvlies\PublishBundle\Entity\Application $app
//          */
//         $app = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->findOneById($id);
//
//         /**
//          * @var \Netvlies\PublishBundle\Services\GitBitbucket $gitService
//          */
//         $gitService = $this->get('git');
//         $gitService->setApplication($app);
//         $sSiteRepository = $gitService->getAbsolutePath();
//
//         // Get new console and execute git command
//         if (!file_exists($sSiteRepository)) {
//             throw new \Exception('Local git repository doesnt exist');
//         }
//
//         $scriptBuilder = new ScriptBuilder(time());
//         $scriptBuilder->addLine('cd '.escapeshellarg($sSiteRepository));
//         $scriptBuilder->addLine('git fetch');
//         $scriptBuilder->addLine('git checkout '.$app->getBranchToFollow());
//
//
//         // Return normal response
//         return array(
//              'scriptpath' => $scriptBuilder->getEncodedScriptPath(),
//              'application' => $app
//         );
//     }


    /**
 	 * @Route("/git/checkout/{id}/{reference}")
     * @Template()
 	 */
     public function checkoutAction($id, $reference) {

         // Check if repo base path is there and writable
         $sPath = $this->container->getParameter('repositorypath');
         $oDir = new \SplFileInfo($sPath);

         if (!$oDir->isDir() || !$oDir->isWritable()) {
             throw new Exception('Main repository directory does not exist or is not writable! (' . $sPath . ')');
         }

         $oEntityManager = $this->getDoctrine()->getEntityManager();
         $app = $oEntityManager->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

         /**
          * @var \Netvlies\PublishBundle\Services\GitBitbucket $gitService
          */
         $gitService = $this->get('git');
         $gitService->setApplication($app);
         $sSiteRepository = $gitService->getAbsolutePath();

         // Get new console and execute git command
         $uid = md5(time().rand(0,1000));
         $scriptBuilder = new ScriptBuilder($uid);
         $scriptBuilder->addLine('cd '.escapeshellarg($sSiteRepository));
         $scriptBuilder->addLine('git fetch');
         $scriptBuilder->addLine('git checkout '.$reference);


         $log = new DeploymentLog();
         $log->setCommand(file_get_contents($scriptBuilder->getScriptPath()));
         $log->setDatetimeStart(new \DateTime());

         $user = array_key_exists('PHP_AUTH_USER', $_SERVER)? $_SERVER['PHP_AUTH_USER'] : 'nobody';
         $log->setUser($user);
         $log->setUid($uid);

         $em  = $this->getDoctrine()->getEntityManager();
         $em->persist($log);
         $em->flush();

         // Return normal response
         return array(
              'scriptpath' => $scriptBuilder->getEncodedScriptPath(),
              'application' => $app,
              'reference' => $reference
         );
     }
}
