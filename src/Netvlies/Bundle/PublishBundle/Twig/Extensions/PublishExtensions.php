<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Twig\Extensions;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;
use Twig_Extension;

class PublishExtensions extends Twig_Extension
{
    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }


    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('applicationselect', array($this, 'getApplicationSelect'), array('id')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('apptypelabel', array($this, 'getApplicationTypeLabel'), array('keyname'))
        );
    }

    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('checkedout', array($this, 'isApplicationCheckedOut', array('id')))
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'publish_extensions';
    }


    public function getApplicationTypeLabel($keyname)
    {
        $appTypes = $this->container->getParameter('netvlies_publish.applicationtypes');

        if(isset($appTypes[$keyname]) && isset($appTypes[$keyname]['label'])){
            return $appTypes[$keyname]['label'];
        }

        return $keyname;
    }

    /**
     * @return string
     */
    public function getApplicationSelect($id=null)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $apps = $em->getRepository('NetvliesPublishBundle:Application')->findAll();
        $current = '';
        $url = '';

        if(!empty($id)){
            $current = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);
        }

        // Generate default route for first app (if present) and replace id with %s, to generate a route template for dashboard
        if(count($apps)){
            $url = $this->container->get('router')->generate('netvlies_publish_application_dashboard', array('id'=> $apps[0]->getId()), null);
            $url = str_replace($apps[0]->getId(), '%s', $url);
        }

        return $this->container->get('templating')->render('NetvliesPublishBundle:Application:select.html.twig', array(
            'dashboardurl'=> $url,
            'apps'=>$apps,
            'current'=>$current)
        );
    }


    public function isApplicationCheckedOut($id)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        /**
         * @var Application $application
         */
        $application = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);

        /**
         * @var VersioningInterface $versioning
         */
        $versioning = $this->container->get($application->getScmService());

        return file_exists($versioning->getRepositoryPath($application));
    }
}