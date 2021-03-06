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

use Netvlies\Bundle\PublishBundle\ApplicationType\ApplicationType;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Router;
use Twig_Extension;

class PublishExtensions extends Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        $twig = $container->get('twig');
        $twig->addGlobal('status_bitbucket', $container->getParameter('netvlies_publish.bitbucket'));
        $twig->addGlobal('status_github', $container->getParameter('netvlies_publish.github'));
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('applicationselect', array($this, 'getApplicationSelect'), array('id')),
            new \Twig_SimpleFunction('usercontentoverlap', array($this, 'getUserContentOverlap'), array('id')),
            new \Twig_SimpleFunction('renderMenu', array($this, 'renderMenu'), array('is_safe' => array('html'))),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('apptypelabel', array($this, 'getApplicationTypeLabel'), array('keyname')),
            new \Twig_SimpleFilter('timediff', array($this, 'getTimeDiff'), array('datetime'))
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

    /**
     * With this method it is possible to inject "application" as a global variable in twig templates
     * see layout.html.twig where this function is used
     *
     * @return Application
     */
    public function renderMenu()
    {
        /**
         * @var Request $request
         */
        $request = $this->container->get('request');
        $logger = $this->container->get('logger');
        $ctlResolver = new ControllerResolver($logger);
        $ctl = $ctlResolver->getController($request);
        $arguments = $ctlResolver->getArguments($request, $ctl);

        $ctlPath = (explode('\\', get_class($ctl[0])));
        $menuTab = (str_replace('Controller', '', array_pop($ctlPath)));
        $application = null;

        foreach ($arguments as $argument) {
            if ($argument instanceof Application) {
                $application = $argument;
                break;
            }

            if (method_exists($argument, 'getApplication')) {
                $application = $argument->getApplication();
                break;
            }
        }

        return $this->container->get('templating')->render('NetvliesPublishBundle::menu.html.twig', array(
            'application' => $application,
            'menuTab' => $menuTab
        ));
    }

    public function getApplicationTypeLabel($keyname)
    {
        if ($this->container->has($keyname)) {
            /**
             * @var ApplicationType $applicationType
             */
            $applicationType = $this->container->get($keyname);
            return $applicationType->getLabel();
        }

        return ucfirst($keyname);
    }

    /**
     */
    public function getTimeDiff(\DateTime $dateTime)
    {
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $dateTime->getTimestamp();

        switch(true){

            case $diff < 0 || $diff < 10 * 60:
                return 'a moment ago';
            case $diff > 10 * 60 && $diff < 60 * 60:
                return ceil($diff/60).' minutes ago';
            case $diff > 60 * 60 && $diff < 60 * 60 * 24:
                $hours = ceil($diff/60/60);
                return $hours == 1 ? 'an hour ago' : $hours.' hours ago';
            case $diff > 60 * 60 * 24 && $diff < 30 * 60 * 60 * 24:
                $days = ceil($diff/60/60/24);
                return $days == 1 ? 'a day ago' : $days.' days ago';
            default:
                $months = ceil($diff/60/60/24/(365/12));
                return $months == 1 ? 'a month ago' : $months.' months ago';

        }
    }

    /**
     * @return string
     */
    public function getApplicationSelect($id = null)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $apps = $em->getRepository('NetvliesPublishBundle:Application')->getAll();
        $current = '';
        $url = '';

        if(!empty($id)){
            $current = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);
        }

        // Generate default route for first app (if present) and replace id with %s, to generate a route template for dashboard
        if(count($apps)){
            $url = $this->container->get('router')->generate('netvlies_publish_application_dashboard', array('application'=> $apps[0]->getId()), null);
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



    public function getUserContentOverlap($id)
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

        $userFiles = $application->getUserFiles();
        $overlaps = array();


        foreach($userFiles as $userFile){
            /**
             * @var UserFile $userFile
             */
            $path = $versioning->getRepositoryPath($application) . DIRECTORY_SEPARATOR . $userFile->getPath();

            if(file_exists($path)){
                if($userFile->getType() == 'D' && is_dir($path)){
                    $overlaps[] = $userFile->getPath();
                }
                if($userFile->getType() == 'F' && is_file($path)){
                    $overlaps[] = $userFile->getPath();
                }
            }
        }

        return $overlaps;
    }
}
