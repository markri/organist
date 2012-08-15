<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/9/12
 * Time: 12:43 AM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishBundle\Twig\Extensions;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Function_Method;
use Netvlies\Bundle\PublishBundle\Form\ApplicationsSelectType;
use Netvlies\Bundle\PublishBundle\Form\Model\ApplicationSelect as ApplicationSelectModel;
use Symfony\Component\Form\FormFactoryInterface;

class ApplicationSelect extends Twig_Extension
{


    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }


    public function getFunctions()
    {
        return array(
            'applicationselect' => new \Twig_Function_Method($this, 'getApplicationSelect', array('id'))
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'application_select_extension';
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

}
