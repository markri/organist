<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VersioningChoiceList extends AbstractType
{

    private $branches;

    public function __construct($branches)
    {
        $this->branches = $branches;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'choices' => array_combine($this->branches, $this->branches)
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'versioning_choicelist';
    }
}
