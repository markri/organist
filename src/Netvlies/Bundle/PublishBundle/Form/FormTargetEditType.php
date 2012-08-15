<?php

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Doctrine\ORM\EntityRepository;
use Netvlies\Bundle\PublishBundle\Form\DataTransformer\IdToEnvironment;

class FormTargetEditType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('environment', 'environment_selector', array(
                'label' => 'Environment *',
                'required' => true,
            ))
            ->add('username', 'text', array(
                'label'=>'Username. (in which homedir it will be deployed, just username) *',
                'required'=>true,
            ))
            ->add('label', 'text', array(
                'label'=>'Label (e.g. "Settings for myapp.dev1.netvlies.net") *',
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
                'label'=>'Capistrano root')
            );


    }


    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;
        return $options;
    }


    public function getName()
    {
        return 'netvlies_publishbundle_targetedittype';
    }

}

