<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/8/12
 * Time: 11:04 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishParametersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


class MysqlForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username', 'text', array())
            ->add('password', 'password', array())
            ->add('dbname', 'text', array())
            ->add('hostname', 'text', array())
            ->add('port', 'text', array())
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'mysql_parameters';
    }

}
