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

class StrategyType extends AbstractType
{
    private $strategies;

    public function __construct($strategies)
    {
        $this->strategies = $strategies;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'choices' => $this->strategies,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'strategy_choicelist';
    }
}
