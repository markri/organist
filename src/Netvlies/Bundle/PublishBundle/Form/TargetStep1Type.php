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

class TargetStep1Type extends HorizontalAbstractType
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
            ));
    }


    public function getName()
    {
        return 'netvlies_publishbundle_target_step1';
    }

}
