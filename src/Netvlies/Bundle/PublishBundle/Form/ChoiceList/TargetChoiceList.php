<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

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

        foreach($targets as $target){
            $return[$target->getId()] = $target;
        }

        return $return;
    }



    public function getParent()
    {
        return 'entity';
    }


    /**
     * @param array $options
     * @return array
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {

        $options->setDefaults(
            array(
                'class' => 'NetvliesPublishBundle:Target',
                'label' => false,
                'empty_value' => '-- Choose a target --',
                'app' => null,
                'choices' => function (Options $options){
                    return $this->getChoices($options['app']);
                }
        ));
    }

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
