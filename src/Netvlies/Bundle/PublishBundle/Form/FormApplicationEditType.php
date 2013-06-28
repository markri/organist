<?php

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class FormApplicationEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', array(
                'label' => 'Name *')
            )
            ->add('customer', 'text', array(
                'label' => 'Customer / Groupname',
                'required' => false)
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