<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 7/22/13
 * Time: 2:09 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class UserFile extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('path');
        $builder->add('type', 'choice', array(
            'choices' => array('D'=> 'Directory', 'F'=>'File'),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Netvlies\Bundle\PublishBundle\Entity\UserFile',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'userfile_type';
    }


}