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
use Symfony\Component\Form\FormEvents;

class FormTargetEditType extends AbstractType
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
                'attr' => array('data-help'=> 'in which homedir it will be deployed, just username. This will be used on SSH connection'),
                'required'=>true,
            ))
            ->add('label', 'text', array(
                'label'=>'Label *',
                'attr' => array('data-help' => 'e.g. "(P) www.mywonderfullsite.com"'),
                'required'=>true)
            )
            ->add('primarydomain', 'text', array(
                'label'=>'Primary domain *',
                'attr' => array('data-help' => 'e.g. www.mywonderfullsite.com'),
                'required'=>true)
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