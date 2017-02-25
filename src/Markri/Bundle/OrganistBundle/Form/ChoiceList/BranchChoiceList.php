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

use Symfony\Component\Form\AbstractType;
use Markri\Bundle\OrganistBundle\Entity\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class BranchChoiceList extends AbstractType
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    protected $container;

    /**
     * @param $em
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Loads the choice list
     * Should be implemented by child classes.
     *
     */
    protected function getChoices(Application $app)
    {
        /**
         * @var \Markri\Bundle\OrganistBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->container->get($app->getScmService());
        $branches = $versioningService->getBranches($app);
        $choices = array();

        foreach($branches as $branch){
            /**
             * @var \Markri\Bundle\OrganistBundle\Versioning\ReferenceInterface $branch
             */
            $choices[$branch->getName()] = $branch->getName();
        }

        return $choices;
    }



    public function getParent()
    {
        return 'choice';
    }


    /**
     * @param array $options
     * @return array
     */
    public function setDefaultOptions(OptionsResolverInterface $options)
    {

        $options->setDefaults(
            array(
                'label' => false,
                'empty_value' => '-- Choose a branch --',
                'app' => null,
                'choices' => function (Options $options){
                    return $this->getChoices($options['app']);
                }
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'branch_choicelist';
    }
}
