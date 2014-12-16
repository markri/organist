<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 12/11/14
 * Time: 5:50 PM
 */

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ScheduledDeploymentType extends HorizontalAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $options['app'];

        $builder
            ->add('target', 'target_choicelist', array(
                'label' => 'Target *',
                'app' => $app,
                'required' => true
            ))
            ->add('branch', 'branch_choicelist', array(
                'label' => 'Branch *',
                'app' => $app,
                'required' => true
            ))
            ->add('time', 'time', array(
                'label' => 'Time *',
                'input' => 'string',
                'widget' => 'choice',
                'required' => true
            ));

    }


    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'app' => null,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'scheduled_deployment';
    }


} 