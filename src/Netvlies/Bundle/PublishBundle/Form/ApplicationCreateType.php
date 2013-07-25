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
            ->add('name', 'text', array(
                'label' => 'Name *')
            )
            ->add('customer', 'text', array(
                'label' => 'Customer / Groupname',
                'required' => false)
            )
            ->add('keyname', 'text', array(
                'max_length'=>16,
                'label'=>'Unique technical name (max 16 chars) * ',
                'required' => true)
            )
            ->add('applicationType', 'applicationtype_choicelist', array(
                    'label'=>'Application type *',
                    'required'=>true )
            )
            ->add('scmService', 'versioning_choicelist', array(
                'label'=>'Versioning service *',
                'required'=>true )
            )
            ->add('scmUrl', 'text', array(
                'label'=>'Versioning URL (e.g. git@bitbucket.org:netvlies/my_project.git) *',
                'required'=>true )
            );
    }

    public function getDefaultOptions(array $options)
    {
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
