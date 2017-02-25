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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationRollbackType extends HorizontalAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $options['app'];

        $builder
            ->add('target', 'target_choicelist', array(
                'label' => 'Target *',
                'app' => $app,
                'attr' => array('data-help'=>'This works just like ctrl-z. (It will undo last succesfull deployment).'),
                'required' => true
            ));

    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(
            array(
                'app' => null
            )
        );
    }

    public function getName()
    {
        return 'markri_organistbundle_applicationrollback';
    }

}
