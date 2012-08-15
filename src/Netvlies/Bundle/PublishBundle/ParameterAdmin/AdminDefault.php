<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/8/12
 * Time: 10:44 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishBundle\ParameterAdmin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;


abstract class AdminDefault implements AdminInterface
{

    protected $container;

    protected $entity;


    /**
     * In this method you will likely use the implemented getForm method and do a bind action on it with given request
     * @return mixed
     */
    public function bind(Request $request)
    {
        $this->getForm()->bind($request);
    }

    /**
     * This method will give you some room to implement custom validation. Also the normal $form->isValid must be
     * implemented here
     *
     * @return mixed
     */
    public function validate()
    {
        return $this->getForm()->isValid();
    }

    /**
     * This method must implement the storage of your parameterset
     */
    public function persist()
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $em->persist($this->getEntity());
    }


    /**
     * Must return the parameterset entity
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * In most cases this doesnt need to be implemented, but if you're using a different entity manager, other
     * than default, you can call the flush method in here
     *
     * @return mixed
     */
    public function flush()
    {
        return;
    }

    /**
     * @param $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }


}