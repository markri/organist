<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/9/12
 * Time: 12:43 AM
 * To change this template use File | Settings | File Templates.
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

        if(!empty($id)){
            $current = $em->getRepository('NetvliesPublishBundle:Application')->findOneById($id);
        }

        return $this->container->get('templating')->render('NetvliesPublishBundle:Application:select.html.twig', array('apps'=>$apps, 'current'=>$current));
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