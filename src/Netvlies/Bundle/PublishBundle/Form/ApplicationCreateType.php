<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/28/12
 * Time: 10:12 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class ApplicationCreateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('keyname', 'text', array(
                'max_length'=>16,
                'label'=>'Unique keyname',
                'required' => true)
            )
            ->add('name', 'text', array())
            ->add('customer', 'text', array(
                'required' => false)
            )
            ->add('type', 'entity', array(
                'class' => 'Netvlies\Bundle\PublishBundle\Entity\ApplicationType',
                'property'=> 'displayName',
                'label' => 'Application type',
                'empty_value'=> '-- choose type --',
                'expanded' => false,
                'multiple' => false,
                'required'=>true)
            )
            ->add('scmService', 'choice', array(
                'choices' => $options['scmtypes'],
                'label'=>'SCM service',
                'required'=>true )
            );
    }

    public function getDefaultOptions(array $options)
    {
        $options = array(
            'scmtypes' => array()
        );

        return $options;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'application_create';
    }

}
