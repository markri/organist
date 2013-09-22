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

use Doctrine\ORM\EntityRepository;

class FormTargetStep1Type extends AbstractType
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
                'attr' => array('data-help'=> 'in which homedir it will be deployed, just username. User will be used on SSH connection'),
                'required'=>true,
            ));
    }


    public function getName()
    {
        return 'netvlies_publishbundle_target_step1';
    }

}