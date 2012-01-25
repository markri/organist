<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;



class EnvironmentsType implements ChoiceListInterface
{

    private $em;

    public function __construct($em){
        $this->em = $em;
    }


    /**
     * Returns a list of choices
     *
     * @return array
     */
    function getChoices()
    {
		$envs = $this->em->getRepository('NetvliesPublishBundle:Environment')->getOrderedByTypeAndHost();		
		$return = array(''=>'-- Choose environment --');
		
		foreach($envs as $env){
			$return[$env->getId()] = $env->getType().' ('.$env->getHostname().')';
		}
		
		return $return;
    }
}
