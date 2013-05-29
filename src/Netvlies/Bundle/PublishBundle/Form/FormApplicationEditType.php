<?php

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class FormApplicationEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('read_only' => true))
            ->add('customer', 'text', array('read_only' => true))
            ->add('type', 'entity', array(
                'class' => 'NetvliesPublishBundle:ApplicationType',
                'property'=> 'label',
                'expanded' => false,
                'multiple' => false,
                'required'=>true)
            )
            ->add('scmURL', 'text', array('required'=>true, 'label'=>'SCM url to your repository', 'read_only' => true))
            ;

        $builder
            ->add('name', 'text', array(
                'label' => 'Name *')
            )
            ->add('customer', 'text', array(
                'label' => 'Customer / Groupname',
                'required' => false)
            )
            ->add('keyname', 'text', array(
                'read_only'=>true)
            )
            ->add('type', 'entity', array(
                'class' => 'Netvlies\Bundle\PublishBundle\Entity\ApplicationType',
                'property'=> 'displayName',
                'label' => 'Application type *',
                'empty_value'=> '-- choose type --',
                'expanded' => false,
                'multiple' => false,
                'disabled'=>true)
            )
            ->add('scmService', 'text', array(
                'label'=>'SCM service',
                'read_only'=>true )
            )
            ->add('scmUrl', 'text', array(
                'label'=>'SCM URL',
                'read_only'=>true )
        );
    }

    public function getDefaultOptions(array $options)
    {
        return $options;
    }

    public function getName()
    {
        return 'application_edit';
    }

}