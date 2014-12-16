<?php
/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 12/12/14
 * Time: 2:44 PM
 */

namespace Netvlies\Bundle\PublishBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

abstract class HorizontalAbstractType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['attr']['data-horizontal'] = true;
    }
} 