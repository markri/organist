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
use Netvlies\PublishBundle\Form\FormApplicationType;



class GitController extends Controller
{

    /**
     * @Route("/git/clone/{id}")
     * @Template()
     * @param $id
     */
    public function cloneApplicationAction($id){
         $oEntityManager = $this->getDoctrine()->getEntityManager();
         $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Application');


        /**
         * @var \Netvlies\PublishBundle\Entity\Application $oApp
         */
         $oApp = $oRepository->find($id);

         $sRepositoryPath = $this->container->getParameter('repositorypath');
         $oApp->setBaseRepositoriesPath($sRepositoryPath);

        $scriptBuilder = new ScriptBuilder(time());
        $scriptBuilder->addLine('rm -rf '.escapeshellarg($oApp->getAbsolutePath()));
        $scriptBuilder->addLine('git clone '.escapeshellarg($oApp->getGitrepoSSH()).' '.escapeshellarg($oApp->getAbsolutePath()));


        return array('script' => $scriptBuilder->getEncodedScriptPath(),
                     'site' => $oApp);
    }




    /**
 	 * @Route("/git/pull/{id}")
 	 */
//     public function pullAction($id) {
//
//         // Check if repo base path is there and writable
//         $sPath = $this->container->getParameter('repositorypath');
//         $oDir = new \SplFileInfo($sPath);
//
//         if (!$oDir->isDir() || !$oDir->isWritable()) {
//             echo 'Main repository directory does not exist or is not writable! (' . $sPath . ')';
//             return array();
//         }
//
//         // Get site and determine its subfolder to write
//         $oSite = $this->getSite($id);
//         $sSiteRepository = $oSite->getAbsolutePath();//$sPath . '/' . $oSite->getName();
//
//         // Get new console and execute git command
//         /* @var $oConsole Console */
//         $oConsole = $this->container->get('Console');
//
//         if (file_exists($sSiteRepository)) {
//             $oConsole->execute('cd '.escapeshellarg($sSiteRepository).'; git pull');
//         } else {
//             $oConsole->execute('git clone ' . $oSite->getRepository() . ' ' . escapeshellarg($sSiteRepository));
//         }
//
//         // Return normal response
//         return array();
//     }

}
