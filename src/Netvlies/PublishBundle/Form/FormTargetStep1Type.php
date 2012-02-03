<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

use Doctrine\ORM\EntityRepository;


class FormTargetStep1Type extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {

        $builder
            ->add('environment', 'choice', array(
                'choice_list'=>$options['envchoice'],
                'required' => true,
            ))
            ->add('username', 'text', array(
                'label'=>'Username. (in which homedir it will be deployed, just username)',
                'required'=>true,
            ));
    }


    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;
		$options['envchoice'] = null;

        return $options;
    }


    public function getName()
    {
        return 'netvlies_publishbundle_targettype1';
    }

}

