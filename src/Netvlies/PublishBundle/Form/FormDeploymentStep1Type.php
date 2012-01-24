<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;
use Netvlies\PublishBundle\Entity\PhingTargetRepository;
use Netvlies\PublishBundle\Entity\EnvironmentRepository;

class FormDeploymentStep1Type extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $app = $options['app'];
        $builder
			->add('primarydomain', 'text', array('label'=>'Label/domeinnaam', 'required'=>true))
            ->add('environment', 'choice', array(
					'choice_list'=>$options['envchoice'],
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
        return 'netvlies_publishbundle_deploymentstep1type';
    }

}

