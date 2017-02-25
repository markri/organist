<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */


namespace Markri\Bundle\OrganistBundle\Form\ChoiceList;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class EnvironmentType extends AbstractType
{

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'environment_choicelist';
    }


    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * @param array $options
     * @return array
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {

        $options->setDefaults(
            array(
                'class' => 'OrganistBundle:Environment',
                'label' => false,
                'empty_value' => '-- Choose an environment --',
                'choices' => function (Options $options){
                    return $this->getChoices();
                }
            ));
    }

    /**
     * Loads the choice list
     *
     * Should be implemented by child classes.
     *
     * @return array with environements
     */
    protected function getChoices()
    {
        return $this->em->getRepository('OrganistBundle:Environment')->getOrderedByTypeAndHost();
    }
}