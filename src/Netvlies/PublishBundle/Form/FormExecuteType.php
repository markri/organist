<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class FormExecuteType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {

        $builder
            ->add('branchtodeploy', 'choice', array(
                'choice_list'=>$options['branchchoice'],
                'label'=>'Branch/Tag to use'
            ))
            ;
    }

    public function getDefaultOptions(array $options)
    {
        $options['branchchoice'] = null;
        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_executetype';
    }

}

