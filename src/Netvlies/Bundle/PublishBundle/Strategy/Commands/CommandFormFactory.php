<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 7/4/15
 * Time: 10:29 PM
 */

namespace Netvlies\Bundle\PublishBundle\Strategy\Commands;


use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Entity\CommandTemplate;
use Symfony\Component\Form\FormFactory;

class CommandFormFactory
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }


    /**
     * @param CommandTemplate $template
     * @param Application $app
     * @param array $fields
     * @return \Symfony\Component\Form\Form
     * @throws \Exception
     */
    public function createForm(CommandTemplate $template, Application $app, array $fields)
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            'command_form_' . $template->getId(),
            'form',
            null,
            array(

                'label' => $template->getTitle(),
                'attr' => array(
                    'data-horizontal' => true,
                    'class' => 'form-horizontal',
                    'name' => 'command_form_' . $template->getId(),
                )
            )
        );

        foreach ($fields as $field) {

            $fieldType = null;
            $label = null;

            switch($field) {
                case 'revision':
                    $label = 'Revision';
                    $fieldType = 'reference_choicelist';
                    break;
                case 'target':
                    $label = 'Target';
                    $fieldType = 'target_choicelist';
                    break;
                default:
                    // @todo text field?
                    break;
            }

            if(!$fieldType) {
                continue;
            }

            $options = array(
                'label' => $label,
                'required' => true,
                'app' => $app
            );

            $formBuilder->add($field, $fieldType, $options);
        }

        $formBuilder->add('run', 'submit', array('label' => $template->getTitle(), 'attr' => array('class' => 'btn btn-default pull-right')));

        return $formBuilder->getForm();
    }
}
