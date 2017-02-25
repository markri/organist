<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 7/4/15
 * Time: 10:29 PM
 */

namespace Markri\Bundle\OrganistBundle\Strategy\Commands;


use Markri\Bundle\OrganistBundle\Entity\Command;
use Symfony\Component\Form\FormFactory;

class CommandFormFactory
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createForm(Command $command)
    {
        $formConfig = json_decode($command->getFormConfig(), true);
        $formBuilder = $this->formFactory->createNamedBuilder('command_form_' . $command->getId(), 'form', null, array('label' => $command->getLabel()));

        foreach ($formConfig as $key => $fieldDefinition) {
            $fieldType = $fieldDefinition['type'];
            $options = $fieldDefinition['options'];

            if ($fieldType == 'target_choicelist' || $fieldType == 'reference_choicelist') {
                $options['app'] = $command->getApplication();
            }

            $formBuilder->add($key, $fieldType, $options);
        }

        $formBuilder->add('run', 'submit', array('label' => 'Run', 'attr' => array('class' => 'btn btn-default pull-right')));
        $formBuilder->add('preview', 'submit', array('label' => 'Preview', 'attr' => array('class' => 'btn btn-default pull-right')));

        return $formBuilder->getForm();
    }
}
