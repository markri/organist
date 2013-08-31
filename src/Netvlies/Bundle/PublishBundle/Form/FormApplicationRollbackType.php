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

use PhpOption\Option;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Netvlies\Bundle\PublishBundle\Entity\TargetRepository;
use Netvlies\Bundle\PublishBundle\Entity\Rollback;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormApplicationRollbackType extends AbstractType
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
        return 'netvlies_publishbundle_applicationrollback';
    }

}
