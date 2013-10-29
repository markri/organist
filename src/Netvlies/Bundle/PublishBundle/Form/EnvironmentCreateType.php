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

class EnvironmentCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('type', 'choice', array(
                'choices' => array(
                    'D'=>'D',
                    'T'=>'T',
                    'A'=>'A',
                    'P'=>'P'
                ),
                'label' => 'DTAP type *',
                'required' => true,
            ))
            ->add('hostname', 'text', array(
                'label'=>'Hostname *',
                'attr' => array('data-help'=> 'either IP or DNS'),
                'required'=>true,
            ));
    }


    public function getName()
    {
        return 'netvlies_publishbundle_environment_create';
    }

}