<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;
use Netvlies\PublishBundle\Entity\PhingTargetRepository;

class FormTargetStep2Type extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $app = $options['app'];
        $builder
            ->add('label', 'text', array('label'=>'Label (e.g. "Settings for myapp.dev1.netvlies.net"','required'=>true))
            ->add('primarydomain', 'text', array('label'=>'Primary domain (e.g. myapp.dev1.netvlies.net)', 'required'=>true))
            ->add('mysqldb', 'text', array('required'=>true, 'label'=>'MySQL database'))
            ->add('mysqluser', 'text', array('required'=>true, 'label'=>'MySQL user'))
            ->add('mysqlpw', 'text', array('required'=>true, 'label'=>'MySQL password'))
            ->add('approot', 'text', array('required'=>true, 'label'=>'Absolute approot'))
            ->add('webroot', 'text', array('required'=>true, 'label'=>'Absolute webroot'))
            ->add('requiresrevision', 'checkbox', array('required'=>false, 'label'=>'Requires submission of revision on execution'))
            ->add('phingtargets', 'entity', array(
                                'label' => 'Bound phing target',
                                'property' => 'name',
                                'empty_value' => '-- Choose a Phing target --',
                                'class' => 'NetvliesPublishBundle:PhingTarget',
                                'query_builder' => function(PhingTargetRepository $er) use ($app){
                                    return $er->createQueryBuilder('t')
                                            ->where('t.application = :app')
                                            ->setParameter('app', $app);
                                },
                                'expanded' => true,
                                'multiple' => true,
                                'required' => true
                            ))
            ;
    }

    public function getDefaultOptions(array $options)
    {
        $options['app'] = null;
        $options['branches'] = array();
        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_targettypestep2';
    }

}

