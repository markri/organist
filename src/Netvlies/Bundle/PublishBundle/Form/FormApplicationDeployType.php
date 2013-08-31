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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormApplicationDeployType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $options['app'];

        $builder
            ->add('target', 'target_choicelist', array(
                'label' => 'Target *',
                'app' => $app,
                'required' => true
            ))
            ->add('revision', 'reference_choicelist', array(
                'label' => 'Reference *',
                'app' => $app,
                'required' => true
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
           'app' => null,
        ));
    }

    public function getName()
    {
        return 'netvlies_publishbundle_applicationdeploy';
    }

}
