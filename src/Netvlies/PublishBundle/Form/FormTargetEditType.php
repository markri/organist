<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Doctrine\ORM\EntityRepository;
use Netvlies\PublishBundle\Form\DataTransformer\IdToEnvironment;

class FormTargetEditType extends AbstractType
{


    public function buildForm(FormBuilder $builder, array $options)
    {

        $builder
            ->add('environment', 'environment_selector', array(
                'required' => true,
            ))
            ->add('username', 'text', array(
                'label'=>'Username. (in which homedir it will be deployed, just username)',
                'required'=>true,
            ))
            ->add('label', 'text', array(
                'label'=>'Label (e.g. "Settings for myapp.dev1.netvlies.net"',
                'required'=>true)
            )
            ->add('primarydomain', 'text', array(
                'label'=>'Primary domain (e.g. myapp.dev1.netvlies.net)',
                'required'=>true)
            )
            ->add('mysqldb', 'text', array(
                'required'=>true,
                'label'=>'MySQL database')
            )
            ->add('mysqluser', 'text', array(
                'required'=>true,
                'label'=>'MySQL user')
            )
            ->add('mysqlpw', 'text', array(
                'required'=>true,
                'label'=>'MySQL password')
            )
            ->add('approot', 'text', array(
                'required'=>true,
                'label'=>'Absolute approot')
            )
            ->add('webroot', 'text', array(
                'required'=>true,
                'label'=>'Absolute webroot')
            );



    }


    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;
        $options['em'] = null;
        return $options;
    }


    public function getName()
    {
        return 'netvlies_publishbundle_targetedittype';
    }

}

