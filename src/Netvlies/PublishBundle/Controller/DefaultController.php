<?php

namespace Netvlies\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Netvlies\PublishBundle\Entity\SiteRepository;
use Netvlies\PublishBundle\Entity\Site;
use Netvlies\PublishBundle\Console;



class DefaultController extends Controller {

    /**
	 * @Route("/")
	 * @Template()
	 */
    public function indexAction() {
        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Site');
        $allSites = $oRepository->getAllSites();

        return array('sites' => $allSites);
    }

    /**
     * @Route("/login")
     * @Template()
     */
    public function loginAction(){
        return array();
    }


    /**
	 * @Route("/detail/{id}")
	 * @Template("NetvliesPublishBundle:Default:index.html.twig")
	 */    
    public function detailAction($id) {

        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Site');
        $allSites = $oRepository->getAllSites();

        $oSite = $this->getSite($id);

        $allTwigParams = array();
        $allTwigParams['sites'] = $allSites;
        $allTwigParams['site'] = $oSite;
        $allTwigParams['pullAction'] = !file_exists($oSite->getAbsolutePath().'/'.$oSite->getBuildfile());

        if (!$allTwigParams['pullAction']) {
            // Build file exists so get targets
            $allTwigParams['targets'] = $oSite->parseTargets();
        }

        return $allTwigParams;
    }

    /**
	 * @Route("/pull/{id}")
	 */ 	
    public function pullAction($id) {

        // Check if repo base path is there and writable
        $sPath = $this->container->getParameter('repositorypath');
        $oDir = new \SplFileInfo($sPath);

        if (!$oDir->isDir() || !$oDir->isWritable()) {
            echo 'Main repository directory does not exist or is not writable! (' . $sPath . ')';
            return array();
        }

        // Get site and determine its subfolder to write
        $oSite = $this->getSite($id);
        $sSiteRepository = $oSite->getAbsolutePath();//$sPath . '/' . $oSite->getName();

        // Get new console and execute git command
        /* @var $oConsole Console */
        $oConsole = $this->container->get('Console');

        if (file_exists($sSiteRepository)) {
            $oConsole->execute('cd '.escapeshellarg($sSiteRepository).'; git pull');
        } else {
            $oConsole->execute('git clone ' . $oSite->getRepository() . ' ' . escapeshellarg($sSiteRepository));
        }

        // Return normal response
        return array();
    }
    
    /**
	 * @Route("/detail/{id}/target/{name}")
	 */     
    public function execTargetAction($id, $name){
        $oSite = $this->getSite($id);
        $oConsole = $this->container->get('Console');
        $oConsole->execute('phing -f '.escapeshellarg($oSite->getAbsolutePath().$oSite->getBuildfile()).' '.escapeshellarg($name));
        // Return normal response
        return array();
    }


    protected function getSite($id){
        $oEntityManager = $this->getDoctrine()->getEntityManager();
        $oRepository = $oEntityManager->getRepository('NetvliesPublishBundle:Site');
        $oSite = $oRepository->find($id);

        $sRepositoryPath = $this->container->getParameter('repositorypath');
        $docRoot = dirname($sRepositoryPath);
        $sRepositoryPath.= '/' . $oSite->getName() . '/';

        $oSite->setAbsolutePath($sRepositoryPath);

        $oSite->setBrowsePath(str_replace($docRoot, '', $sRepositoryPath));

        return $oSite;
    }


}
