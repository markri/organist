<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 1/29/12
 * Time: 1:22 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Netvlies\Bundle\PublishBundle\Entity\TargetRepository;
use Netvlies\Bundle\PublishBundle\Entity\Deployment;
use Netvlies\Bundle\PublishBundle\Form\ChoiceList\EnvironmentsType as EnvironmentChoice;
use Netvlies\Bundle\PublishBundle\Form\DataTransformer\IdToEnvironment;
use Symfony\Component\Form\FormBuilderInterface;


class EnvironmentType extends AbstractType
{

    private $em;

    public function __construct($em){
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $envChoice = new EnvironmentChoice($this->em);
        $builder
                ->add('environment', 'choice', array(
                        'label' => ' ',
                        'choice_list'=>$envChoice,
                        'required' => true,
                ))
                ->appendClientTransformer(new IdToEnvironment($this->em));;

    }

    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;

        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_envtype';
    }

}
