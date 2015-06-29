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

class ApplicationsSelectType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('application', 'entity', array(
                'class' => 'Netvlies\Bundle\PublishBundle\Entity\Application',
                'property' => 'keyName'
            )
        );
    }


    public function getName()
    {
        return 'netvlies_publishbundle_application_select';
    }

}
