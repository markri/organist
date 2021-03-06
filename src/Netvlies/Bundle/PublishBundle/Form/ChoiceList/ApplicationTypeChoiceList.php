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

class ApplicationTypeChoiceList extends AbstractType
{
    private $appTypes;

    public function __construct($appTypeServices)
    {
        $this->appTypes = $appTypeServices;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(array(
            'choices' => $this->appTypes,
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
