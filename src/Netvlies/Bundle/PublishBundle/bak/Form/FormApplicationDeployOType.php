<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 1/29/12
 * Time: 1:22 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Netvlies\Bundle\PublishBundle\Entity\TargetRepository;


class FormApplicationDeployOType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $options['app'];

        $builder
            ->add('target', 'entity', array(
                'class' => 'NetvliesPublishBundle:Target',
                'query_builder' => function(TargetRepository $tr) use ($app) {
                    return $tr->createQueryBuilder('t')
                        ->innerJoin('t.environment', 'e')
                        ->where('t.application = :app AND e.type = :type')
                        ->setParameter('app', $app)
                        ->setParameter('type', 'O');
                },
                'property'=>'label',
                'required'=>true)
            )
            ->add('revision', 'choice', array(
                'choice_list'=>$options['branchchoice'],
                'label'=>'Branch/Tag to use'
            ));
    }

    public function getDefaultOptions(array $options)
    {
        $options['branchchoice'] = null;
        $options['app'] = null;
        $options['csrf_protection'] = false;

        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_applicationsetupO';
    }

}
