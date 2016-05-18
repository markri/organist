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
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class ReferenceChoiceList extends AbstractType
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
     */
    protected function getChoices(Application $app)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface $versioningService
         */
        $versioningService = $this->container->get($app->getScmService());
        $references = $versioningService->getBranchesAndTags($app);
        $choiceKeys = array();
        $choiceValues = array();

        foreach($references as $reference){
            /**
             * @var \Netvlies\Bundle\PublishBundle\Versioning\ReferenceInterface $reference
             */
            $choiceKeys[] = $reference->getReference();
            $choiceValues[] = $reference->getName();
        }

        return new ChoiceList($choiceKeys, $choiceValues);
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
                'empty_value' => '-- Choose a reference --',
                'app' => null,
                'choice_list' => function (Options $options){
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
        return 'reference_choicelist';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /**
         * @var SimpleChoiceList $choiceList
         */
        $choiceList = $options['choice_list'];

        if (count($choiceList->getChoices()) > 10) {
            $view->vars['attr']['class'] = 'bigrevisionselect';
            $view->vars['attr']['style'] = 'width: 100%';
        } else {
            $view->vars['attr']['class'] = 'form-control';
        }
    }
}
