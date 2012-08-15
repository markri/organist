<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;



class BranchesType implements ChoiceListInterface
{

    private $branches;

    public function __construct($branches){
        $this->branches = $branches;
    }


    /**
     * Returns a list of choices
     *
     * @return array
     */
    function getChoices()
    {
        $return = array(''=>'-- Choose a reference --');
        return array_merge($return, $this->branches);
    }
}
