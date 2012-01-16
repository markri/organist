<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class FormApplicationEnrichType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('read_only' => true))
            ->add('customer', 'text', array('read_only' => true))
            ->add('type', 'choice', array('choices' => array('OMS'=>'OMS', 'Symfony2'=>'Symfony2', "Basissite v1"=>"Basissite v1", 'custom'=>'Custom'), 'empty_value'=> '-- choose type --', 'required'=>true))
            ->add('mysqlpw', 'text', array('label'=>'MySQL wachtwoord', 'required'=>true ))

            ;
    }

    public function getDefaultOptions(array $options)
    {
        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_applicationtype';
    }

}