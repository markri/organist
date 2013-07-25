<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\AbstractType;

class ApplicationTypeChoiceList extends AbstractType
{

    private $appTypes;

    public function __construct($appTypes)
    {
        $this->appTypes = $appTypes;
    }

    public function getDefaultOptions(array $options)
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

        return array(
            'choices' => array_combine($keys, $labels)
        );
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
