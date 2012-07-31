<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Doctrine\ORM\EntityRepository;

class FormTargetStep2Type extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text', array(
                'label'=>'Label (e.g. "(P) www.myapp.com" *',
                'required'=>true)
            )
            ->add('primarydomain', 'text', array(
                'label'=>'Primary domain (e.g. myapp.dev1.netvlies.net) *',
                'required'=>true)
            )
            ->add('mysqldb', 'text', array(
                'required'=>false,
                'label'=>'MySQL database')
            )
            ->add('mysqluser', 'text', array(
                'required'=>false,
                'label'=>'MySQL user')
            )
            ->add('mysqlpw', 'text', array(
                'required'=>false,
                'label'=>'MySQL password')
            )
            ->add('approot', 'text', array(
                'required'=>true,
                'label'=>'Absolute approot *')
            )
            ->add('webroot', 'text', array(
                'required'=>true,
                'label'=>'Absolute webroot *')
            )
            ->add('caproot', 'text', array(
                'required'=>false,
                'label'=>'Absolute capistrano root')
            );
    }


    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;
        return $options;
    }


    public function getName()
    {
        return 'netvlies_publishbundle_targettype2';
    }

}

