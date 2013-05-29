<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Netvlies\Bundle\PublishBundle\Form\ApplicationsSelectType;
use Netvlies\Bundle\PublishBundle\Form\Model\ApplicationSelect;
use GitElephant\Repository;


class DefaultController extends Controller {

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
//        $bla = new Repository('/home/vagrant/repos/boerenmedical');
        // @todo This command should be run in anyterm (make command in symfony, and run it through anyterm)
       // $bla->updateAllBranches();
        return array();
    }

    /**
     * @Route("/login")
     * @Template()
     */
    public function loginAction()
    {
        return array();
    }


    /**
     * @Route("/oops")
     * @Template();
     */
    public function oopsAction()
    {
        return array();
    }



}
