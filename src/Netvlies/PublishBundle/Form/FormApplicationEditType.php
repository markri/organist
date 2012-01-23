<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class FormApplicationEditType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('read_only' => true))
            ->add('customer', 'text', array('read_only' => true))
            ->add('mysqlpw', 'text', array('label'=>'MySQL password', 'read_only' => true))
            ->add('type', 'entity', array(
                'class' => 'NetvliesPublishBundle:ApplicationType',
                'property'=> 'label',
                'expanded' => false,
                'multiple' => false,
                'required'=>true)
            )
            ->add('gitrepoSSH', 'text', array('required'=>true))

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