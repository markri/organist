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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Netvlies\Bundle\PublishBundle\Entity\TargetRepository;
use Netvlies\Bundle\PublishBundle\Entity\Deployment;



class FormApplicationDeployType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $options['app'];

        $builder
            ->add('target', 'target_selector', array(
                'app' => $app,
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
        return 'netvlies_publishbundle_applicationdeploy';
    }

}