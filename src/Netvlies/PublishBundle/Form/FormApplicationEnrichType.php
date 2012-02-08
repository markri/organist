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
            ->add('type', 'entity', array(
                'class' => 'NetvliesPublishBundle:ApplicationType',
                'property'=> 'label',
                'label' => 'Application type',
                'empty_value'=> '-- choose type --',
                'expanded' => false,
                'multiple' => false,
                'required'=>true)
            )
            ->add('mysqlpw', 'text', array('label'=>'Default MySQL password', 'required'=>true ))
            ;
    }

    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;
        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_applicationtype';
    }

}