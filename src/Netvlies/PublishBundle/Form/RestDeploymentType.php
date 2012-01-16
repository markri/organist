<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Netvlies\PublishBundle\Form\DataTransformer\KeyToApplication;
use Netvlies\PublishBundle\Form\DataTransformer\KeyToEnvironment;

class RestDeploymentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('application', 'text')
            ->add('environment', 'text')
            ->add('username', 'text');

        $toApp = new KeyToApplication($options['entitymanager']);
        $toEnv = new KeyToEnvironment($options['entitymanager']);

        $builder->prependClientTransformer($toApp);


        $builder->prependClientTransformer($toEnv);

    }

    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;
        $options['entitymanager'] = null;

        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_environmenttype';
    }

}