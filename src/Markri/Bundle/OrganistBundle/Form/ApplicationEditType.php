<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Form;

use Markri\Bundle\OrganistBundle\Form\ChoiceList\StrategyType;
use Symfony\Component\Form\FormBuilderInterface;

class ApplicationEditType extends HorizontalAbstractType
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
            ->add('buildStatusBadge1', 'text', array(
                'label' => 'Build badge 1',
                'required' => false)
            )
            ->add('buildStatusBadge2', 'text', array(
                'label' => 'Build badge 2',
                'required' => false)
            )
            ->add('deploymentStrategy', StrategyType::class
            )
            ->add('userFiles', 'onetomany', array(
                'label' => 'Shared files and directories',
                'type' => new UserFileType(),
                'allow_delete' => true,
                'allow_add'    => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__name__',
                'layoutmacro' => 'OrganistBundle:Form:macro-onetomany-userfiles.html.twig'
                )
            );
    }


    public function getName()
    {
        return 'application_edit';
    }

}
