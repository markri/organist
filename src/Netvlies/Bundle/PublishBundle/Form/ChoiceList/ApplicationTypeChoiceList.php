<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationTypeChoiceList extends AbstractType
{

    private $appTypes;

    public function __construct($appTypes)
    {
        $this->appTypes = $appTypes;
    }

    public function getChoices()
    {
        $keys = array();
        $labels = array();

        foreach($this->appTypes as $key=>$value){
            $keys[] = $key;
            if(isset($value['label'])){
                $labels[] = $value['label'];
            }
            else{
                $labels[] = $key;
            }
        }

        return  array_combine($keys, $labels);
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'choices' => $this->getChoices(),
        ));
    }


    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'applicationtype_choicelist';
    }
}
