<?php

namespace Netvlies\Bundle\PublishBundle\Form;

use Netvlies\Bundle\PublishBundle\Form\Type\UserFile;
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
            )
            ->add('userFiles', 'collection', array(
                'label' => 'User files and directories',
                'type' => new UserFile(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
            )
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