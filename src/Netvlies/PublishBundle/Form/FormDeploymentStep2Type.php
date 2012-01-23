<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;
use Netvlies\PublishBundle\Entity\PhingTargetRepository;

class FormDeploymentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $app = $options['app'];
        $builder
			->add('label', 'text', array('label'=>'Label/domeinnaam', 'required'=>true))
            ->add('environment', 'entity', array(
                    'empty_value' => '-- Choose an environment --',
                    'class' => 'NetvliesPublishBundle:Environment',
                    'expanded' => false,
                    'multiple' => false,
                    'required' => true
                ))
                ->add('phingtarget', 'entity', array(
                                    'label' => 'Bound phing target',
                                    'property' => 'name',
                                    'empty_value' => '-- Choose a Phing target --',
                                    'class' => 'NetvliesPublishBundle:PhingTarget',
                                    'query_builder' => function(PhingTargetRepository $er) use ($app){
                                        return $er->createQueryBuilder('t')
                                                ->where('t.application = :app')
                                                ->setParameter('app', $app);
                                    },
                                    'expanded' => false,
                                    'multiple' => false,
                                    'required' => true
                                ))
            ->add('username', 'text', array('label'=>'Username. (in which homedir it will be deployed, just username)', 'required'=>true))
            ->add('mysqldb', 'text', array('required'=>true, 'label'=>'MySQL database'))
            ->add('mysqluser', 'text', array('required'=>true, 'label'=>'MySQL user'))
            ->add('mysqlpw', 'text', array('required'=>true, 'label'=>'MySQL password'))
            ->add('approot', 'text', array('required'=>true, 'label'=>'Absolute approot'))
            ->add('webroot', 'text', array('required'=>true, 'label'=>'Absolute webroot'))
            ->add('requiresrevision', 'checkbox', array('required'=>false, 'label'=>'Requires submission of revision on execution'))

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
        return 'netvlies_publishbundle_deploymenttype';
    }

}

