<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;
use Netvlies\PublishBundle\Entity\PhingTargetRepository;
use Netvlies\PublishBundle\Entity\EnvironmentRepository;

class FormTargetStep1Type extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $app = $options['app'];
        $builder
            ->add('label', 'text', array('label'=>'Label','required'=>true))
			->add('primarydomain', 'text', array('label'=>'Primary domain (e.g. www.myapp.com)', 'required'=>true))
            ->add('environment', 'choice', array(
					'choice_list'=>$options['envchoice'],
                    'required' => true
                ))
            ;
    }

    public function getDefaultOptions(array $options)
    {
        $options['app'] = null;
		$options['envchoice'] = null;
        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_targetstep1type';
    }

}

