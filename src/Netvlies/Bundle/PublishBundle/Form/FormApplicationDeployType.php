<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 1/29/12
 * Time: 1:22 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Netvlies\Bundle\PublishBundle\Entity\TargetRepository;
use Netvlies\Bundle\PublishBundle\Entity\Deployment;



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

    public function getDefaultOptions(array $options)
    {
        $options['app'] = null;
        $options['csrf_protection'] = false;

        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_applicationdeploy';
    }

}
