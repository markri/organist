<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Form\ChoiceList;

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
        $targets = $this->em->getRepository('OrganistBundle:Target')->getOrderedByDTAP($app);
        $return = array();

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
                'class' => 'OrganistBundle:Target',
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
