<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/5/12
 * Time: 10:23 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\ParameterAdmin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

interface AdminInterface
{

    /**
     * This method must return the form that you create for your parameterset
     * @return Form
     */
    public function getForm();

    /**
     * This must return all parameter keys, that are eventually used in the command
     * @return array
     */
    public function getKeys();

    /**
     * This must return all parameters in a key value array. It must use the same keys that are returned with
     * the getKeys method
     *
     * @return array
     */
    public function getParameters();

    /**
     * In this method you will likely use the implemented getForm method and do a bind action on it with given request
     */
    public function bind(Request $request);

    /**
     * This method will give you some room to implement custom validation. Also the normal $form->isValid must be
     * implemented here
     *
     * @return boolean
     */
    public function validate();


    /**
     * This method must implement the storage of your parameterset
     */
    public function persist();


    /**
     * In most cases this doesnt need to be implemented, but if you're using a different entity manager, other
     * than default, you can call the flush method in here
     */
    public function flush();


    /**
     * To have DIC inserted here, you'll have full flexibility to retrieve whatever you need to fullfill all needs
     * of other methods in this class
     *
     * @param $container
     */
    public function setContainer($container);


    /**
     * Must return the parameterset entity
     * @return mixed
     */
    public function getEntity();

}
