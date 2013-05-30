<?php
/**
 * @author: M. de Krijger
 * Creation date: 9-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Form\ChoiceList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     *
     */
    protected function getChoices(Application $app)
    {
        /**
         * @var \Netvlies\Bundle\PublishBundle\Services\Scm\ScmInterface $scmService
         */
        $scmService = $this->container->get($app->getScmService());
        $references = $scmService->getBranchesAndTags($app);
        $choices = array();

        foreach($references as $reference){
            /**
             * @var \Netvlies\Bundle\PublishBundle\Services\Scm\Git\Reference $reference
             */
            $choices[$reference->getReference().$reference->getName()] = $reference->getName();
        }

        return $choices;
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->getChoices($options['app']);

        $builder->add('reference', 'choice', array(
            'label' => 'Reference',
            'virtual' => true,
            'choices' => $choices
        ));
    }


    /**
     * @param array $options
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'app' => null
        );
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
}
