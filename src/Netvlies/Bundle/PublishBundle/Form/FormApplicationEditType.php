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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FormApplicationEditType extends AbstractType
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
            ->add('userFiles', 'onetomany', array(
                'label' => 'Shared files and directories',
                'type' => new UserFileType(),
                'allow_delete' => true,
                'allow_add'    => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__name__',
                'layoutmacro' => 'NetvliesPublishBundle:Form:macro-onetomany-userfiles.html.twig'
                )
            );
    }


    public function getName()
    {
        return 'application_edit';
    }

}