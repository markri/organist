<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $apps = $em->getRepository('NetvliesPublishBundle:Application')->getAll();

        if($this->get('security.context')->getToken()->getUser()!='anon.'){
            $userName = $this->get('security.context')->getToken()->getUser()->getUsername();
        }
        else{
            $userName = 'anonymous';
        }

        $favouriteApps = $em->getRepository('NetvliesPublishBundle:CommandLog')->getFavouriteApplications($userName);

        return array(
            'apps' => $apps,
            'favourites' => $favouriteApps
        );
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
