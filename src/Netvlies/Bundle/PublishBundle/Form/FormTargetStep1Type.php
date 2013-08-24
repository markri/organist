<?php

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

use Doctrine\ORM\EntityRepository;


class FormTargetStep1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('environment', 'environment_choicelist', array(
                'label' => 'Environment *',
                'required' => true,
            ))
            ->add('username', 'text', array(
                'label'=>'Username *',
                'attr' => array('data-help'=> 'in which homedir it will be deployed, just username. User will be used on SSH connection'),
                'required'=>true,
            ));
    }


    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;
        return $options;
    }


    public function getName()
    {
        return 'netvlies_publishbundle_targettype1';
    }

}

