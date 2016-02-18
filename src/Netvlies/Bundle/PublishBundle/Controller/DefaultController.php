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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
            $userName = 'sjopet';
        }

        $favouriteApps = $em->getRepository('NetvliesPublishBundle:CommandLog')->getFavouriteApplications($userName);
        $latest = $em->getRepository('NetvliesPublishBundle:CommandLog')->getLatestDeployments();

        return array(
            'apps' => $apps,
            'favourites' => $favouriteApps,
            'logs' => $latest
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


    /**
     * @Route("/githubstatus", name="default_githubstatus")
     */
    public function githubStatus()
    {
        return new JsonResponse(json_decode(file_get_contents('https://status.github.com/api/status.json')));
    }

}
