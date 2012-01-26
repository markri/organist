<?php

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Doctrine\ORM\EntityRepository;
use Netvlies\PublishBundle\Entity\PhingTargetRepository;
use Netvlies\PublishBundle\Entity\EnvironmentRepository;

class FormTargetType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $app = $options['app'];
        $loadSecondPart = $options['secondPart'];
        //$disabled = $loadSecondPart ? array('disabled'=>'disabled') : array();

        $builder
            ->add('environment', 'choice', array(
                'choice_list'=>$options['envchoice'],
                'required' => true,
            ))
            ->add('username', 'text', array(
                'label'=>'Username. (in which homedir it will be deployed, just username)',
                'required'=>true,
            ));

        $factory = $builder->getFormFactory();

        $addStep2 = function($form) use ($factory, $app) {

            /**
             * @var \Netvlies\PublishBundle\Entity\Application $app
             */
            $form->add($factory->createNamed('text', 'label', null, array('label'=>'Label (e.g. "Settings for myapp.dev1.netvlies.net"','required'=>true)));
            $form->add($factory->createNamed('text', 'primarydomain', 'asdf', array('label'=>'Primary domain (e.g. myapp.dev1.netvlies.net)', 'required'=>true)));
            $form->add($factory->createNamed('text','mysqldb', null, array('required'=>true, 'label'=>'MySQL database')));
            $form->add($factory->createNamed('text','mysqluser', null, array('required'=>true, 'label'=>'MySQL user')));
            $form->add($factory->createNamed('text','mysqlpw', null, array('required'=>true, 'label'=>'MySQL password')));
            $form->add($factory->createNamed('text','approot', null, array('required'=>true, 'label'=>'Absolute approot')));
            $form->add($factory->createNamed('text','webroot', null, array('required'=>true, 'label'=>'Absolute webroot')));
//            $form->add($factory->createNamed('entity', 'phingtargets', null, array(
//                                    'label' => 'Use settings for following phing targets',
//                                    'property' => 'name',
//                                    'empty_value' => '-- Choose a Phing target --',
//                                    'class' => 'NetvliesPublishBundle:PhingTarget',
//                                    'query_builder' => function(PhingTargetRepository $er) use ($app){
//                                        return $er->createQueryBuilder('t')
//                                                ->where('t.application = :app')
//                                                ->setParameter('app', $app);
//                                    },
//                                    'expanded' => true,
//                                    'multiple' => true,
//                                    'required' => true
//                                )));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(DataEvent $event) use ($addStep2, $loadSecondPart) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data == null){
               return;
            }

            // Client data is not binded yet, but we do need to be in PRE_SET_DATA for setting entity values in 2nd pass (initial form creation for all fields)
            if(!$loadSecondPart){
                return;
            }

            $addStep2($form);
        });
    }


    public function getDefaultOptions(array $options)
    {
        $options['app'] = null;
		$options['envchoice'] = null;
        $options['secondPart'] = false;
        $options['csrf_protection'] = false;

        return $options;
    }


    public function getName()
    {
        return 'netvlies_publishbundle_targettype';
    }

}

