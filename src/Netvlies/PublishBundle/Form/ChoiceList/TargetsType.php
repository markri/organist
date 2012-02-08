<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;



class TargetsType implements ChoiceListInterface
{

    private $em;
    private $app;

    public function __construct($em, $app){
        $this->em = $em;
        $this->app = $app;
    }

    /**
     * Returns a list of choices
     *
     * @return array
     */
    public function getChoices()
    {
		$targets = $this->em->getRepository('NetvliesPublishBundle:Target')->getOrderedByOTAP($this->app);
		$return = array(''=>'-- Choose a target --');
		
		foreach($targets as $target){
			$return[$target->getId()] = $target->getLabel();
		}
		
		return $return;
    }
}
