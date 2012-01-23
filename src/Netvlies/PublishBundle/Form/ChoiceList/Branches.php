<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;



class Branches implements ChoiceListInterface
{

    private $application;

    public function __construct($app){

        $this->application = $app;

    }


    /**
     * Returns a list of choices
     *
     * @return array
     */
    function getChoices()
    {
        $return = array(''=>'-- Kies een branch --');
        $branches = $this->application->getRemoteBranches();
        return array_merge($return, $branches);
    }
}
