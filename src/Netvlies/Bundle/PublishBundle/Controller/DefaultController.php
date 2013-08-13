<?php

namespace Netvlies\Bundle\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class DefaultController extends Controller
{

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }


    /**
     * @Route("/connect-fail")
     * @Template()
     * @return array
     */
    public function notAuthorizedAction()
    {
        return array();
    }

}
