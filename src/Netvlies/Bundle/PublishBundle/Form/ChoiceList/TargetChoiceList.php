<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvents;

class TargetChoiceList extends AbstractType
{

    protected $em;

    /**
     * @param $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Loads the choice list
     * Should be implemented by child classes.
     *
     */
    protected function getChoices($app)
    {
        $targets = $this->em->getRepository('NetvliesPublishBundle:Target')->getOrderedByOTAP($app);
        $return = array('0'=>'-- Choose a target --');

        foreach($targets as $target){
            if($target->getEnvironment()->getType()=='O'){
                // Since this form element is only used on dashboard for
                continue;
            }
            $return[$target->getId()] = $target->getLabel();
        }

        return $return;
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->getChoices($options['app']);
        $builder->add('target', 'choice', array(
            'label' => 'Target',
            'virtual' => true,
            'choices' => $choices
        ));
    }


    /**
     * @param array $options
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'app' => null
        );
    }

//
//    /**
//     * @return null|string|\Symfony\Component\Form\FormTypeInterface
//     */
//    public function getParent()
//    {
//        return 'form';
//    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'target_choicelist';
    }
}
