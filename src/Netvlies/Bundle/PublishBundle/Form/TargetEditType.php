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

class TargetEditType extends HorizontalAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('environment', 'environment_choicelist', array(
                'label' => 'Environment *',
                'required' => true,
            ))
            ->add('username', 'text', array(
                'label'=>'Username *',
                'attr' => array('data-help'=> 'this username will be used on SSH connection'),
                'required'=>true,
            ))
            ->add('label', 'text', array(
                'label'=>'Label *',
                'attr' => array('data-help' => 'e.g. "(P) www.mywonderfullsite.com"'),
                'required'=>true)
            )
            ->add('primaryDomain', 'text', array(
                'label'=>'Primary domain *',
                'attr' => array('data-help' => 'e.g. www.mywonderfullsite.com'),
                'required'=>true)
            )
            ->add('domainAliases', 'onetomany', array(
                    'label' => 'Domain aliases',
                    'type' => new DomainAliasType(),
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'prototype' => true,
                    'prototype_name' => '__name__')
            )
            ->add('mysqldb', 'text', array(
                'required'=>false,
                'label'=>'MySQL database')
            )
            ->add('mysqluser', 'text', array(
                'required'=>false,
                'label'=>'MySQL user')
            )
            ->add('mysqlpw', 'text', array(
                'required'=>false,
                'label'=>'MySQL password')
            )
            ->add('approot', 'text', array(
                'required'=>true,
                'label'=>'Absolute approot *')
            )
            ->add('webroot', 'text', array(
                'required'=>true,
                'label'=>'Absolute webroot *')
            )
            ->add('caproot', 'text', array(
                'required'=>false,
                'label'=>'Capistrano root')
            );

    }

    public function getName()
    {
        return 'netvlies_publishbundle_targetedittype';
    }

}
