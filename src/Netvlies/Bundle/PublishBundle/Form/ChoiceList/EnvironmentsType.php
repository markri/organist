<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;


class EnvironmentsType extends LazyChoiceList
{

    private $em;

    public function __construct($em){
        $this->em = $em;
    }

    /**
     * Loads the choice list
     *
     * Should be implemented by child classes.
     *
     * @return ChoiceListInterface The loaded choice list
     */
    protected function loadChoiceList()
    {
        $envs = $this->em->getRepository('NetvliesPublishBundle:Environment')->getOrderedByTypeAndHost();

        $labels = array('0'=>'-- Choose environment --');
        $keys = array('0'=>'0');


        foreach($envs as $env){
            $label = $env->getType().' ('.$env->getHostname().')';
            $keys[] = $env->getId();
            $labels[] = $label;
        }

        return new ChoiceList($keys, $labels);
    }

}
