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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Netvlies\Bundle\PublishBundle\Entity\TargetRepository;
use Netvlies\Bundle\PublishBundle\Form\ChoiceList\TargetsType;
use Netvlies\Bundle\PublishBundle\Form\DataTransformer\IdToTarget;



class TargetType extends AbstractType
{

    private $em;

    public function __construct($em){
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $targetChoice = new TargetsType($this->em, $options['app']);
        $builder
                ->add('target', 'choice', array(
                        'label' => ' ',
                        'choice_list'=>$targetChoice,
                        'required' => true,
                ))
                ->appendClientTransformer(new IdToTarget($this->em));;

    }

    public function getDefaultOptions(array $options)
    {
        $options['csrf_protection'] = false;
        $options['app'];

        return $options;
    }

    public function getName()
    {
        return 'netvlies_publishbundle_targettype';
    }

}
