<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 1/29/12
 * Time: 1:22 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Netvlies\PublishBundle\Entity\TargetRepository;
use Netvlies\PublishBundle\Entity\Deployment;



class FormApplicationDeployType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $app = $options['app'];

        $builder
            ->add('target', 'entity', array(
                'class' => 'NetvliesPublishBundle:Target',
                'query_builder' => function(TargetRepository $tr) use ($app){
                    return $tr->createQueryBuilder('t')
                        ->where('t.application = :app')
                        ->setParameter('app', $app);
                    //@todo order by OTAP
                },
                'empty_value' => '-- Choose a target --',
                'expanded' => false,
                'multiple' => false,
                'required'=>true)
            )
            ->add('reference', 'choice', array(
                'choice_list'=>$options['branchchoice'],
                'label'=>'Branch/Tag to use'
            ));
    }

    public function getDefaultOptions(array $options)
    {
        $options['branchchoice'] = null;
        $options['app'] = null;

        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_applicationdeploy';
    }

}
