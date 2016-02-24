<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class ApplicationCreateType extends HorizontalAbstractType
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
            ->add('applicationType', 'entity', array(
                    'class' => 'Netvlies\Bundle\PublishBundle\Entity\ApplicationType',
                    'property' => 'title',
                    'label'=>'Application type *',
                    'attr' => array('data-help' => 'This has effect on the default shared files and folders'),
                    'required'=>true )
            )
            ->add('scmService', 'versioning_choicelist', array(
                'label'=>'Versioning service *',
                'required'=>true )
            )
            ->add('scmUrl', 'text', array(
                'label'=>'Versioning URL *',
                'attr' => array('data-help' => 'e.g. git@bitbucket.org:netvlies/my_project.git'),
                'required'=>true )
            )
            ->add('deploymentStrategy', 'entity', array(
                    'label' => 'Deployment strategy *',
                    'class' => 'Netvlies\Bundle\PublishBundle\Entity\Strategy',
                    'property' => 'title',
                    'required' => true
                )
            );
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
