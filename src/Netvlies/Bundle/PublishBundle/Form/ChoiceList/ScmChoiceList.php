<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\AbstractType;

class ScmChoiceList extends AbstractType
{

    private $branches;

    public function __construct($branches)
    {
        $this->branches = $branches;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'choices' => array_combine($this->branches, $this->branches)
        );
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'scm_choicelist';
    }
}
