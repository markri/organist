<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class FormApplicationEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('read_only' => true))
            ->add('customer', 'text', array('read_only' => true))
            ->add('mysqlpw', 'text', array('label'=>'Default MySQL password', 'read_only' => true))
            ->add('type', 'entity', array(
                'class' => 'NetvliesPublishBundle:ApplicationType',
                'property'=> 'label',
                'expanded' => false,
                'multiple' => false,
                'required'=>true)
            )
            ->add('scmURL', 'text', array('required'=>true, 'label'=>'SCM url to your repository', 'read_only' => true))
            ;
    }

    public function getDefaultOptions(array $options)
    {
        $options['branchchoice'] = null;
        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_applicationtype';
    }

}