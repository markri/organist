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


class ApplicationsSelectType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('application', 'entity', array(
                'class' => 'Netvlies\Bundle\PublishBundle\Entity\Application',
                'property' => 'keyName'
            )
        );

    }




    public function getDefaultOptions(array $options)
    {
        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_application_select';
    }

}
