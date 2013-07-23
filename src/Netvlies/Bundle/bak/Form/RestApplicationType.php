<?php

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class RestApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('customer', 'text')
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