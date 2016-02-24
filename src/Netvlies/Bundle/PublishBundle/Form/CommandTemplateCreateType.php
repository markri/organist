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

class CommandTemplateCreateType extends HorizontalAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('strategy', 'entity', array(
                    'class' => 'Netvlies\Bundle\PublishBundle\Entity\Strategy',
                    'property' => 'title',
                    'label' => 'Strategy *',
                    'required' => true
                )
            )
            ->add('title', 'text', array(
                    'label' => 'Title *',
                    'required' => true
                )
            )
            ->add('template', 'textarea', array(
                    'label' => 'Twig template *',
                    'required' => true,
                    'attr' => array('data-editor' => 'twig', 'rows' => '30' )
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
        return 'commandtemplate_create';
    }

}
