<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;


class TargetsType extends LazyChoiceList
{

    private $em;
    private $app;

    public function __construct($em, $app){
        $this->em = $em;
        $this->app = $app;
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
        $targets = $this->em->getRepository('NetvliesPublishBundle:Target')->getOrderedByOTAP($this->app);
        $return = array('0'=>'-- Choose a target --');

        foreach($targets as $target){
            if($target->getEnvironment()->getType()=='O'){
                // Since this form element is only used on dashboard for
                continue;
            }
            $return[$target->getId()] = $target->getLabel();
        }

        return new ChoiceList(array_keys($return), $return);


    }

}
