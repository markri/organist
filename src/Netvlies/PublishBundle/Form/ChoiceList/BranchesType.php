<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;



class BranchesType implements ChoiceListInterface
{

    private $gitService;

    public function __construct($gitService){
        $this->gitService = $gitService;
    }


    /**
     * Returns a list of choices
     *
     * @return array
     */
    function getChoices()
    {
        $return = array(''=>'-- Kies een branch --');
        $branches = $this->gitService->getRemoteBranches();
        return array_merge($return, $branches);
    }
}
