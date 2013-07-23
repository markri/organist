<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mdekrijger
 * Date: 8/8/12
 * Time: 10:31 PM
 * To change this template use File | Settings | File Templates.
 */
use Netvlies\Bundle\PublishBundle\ParameterAdmin\AdminTarget;
use Netvlies\Bundle\PublishBundle\Entity\Target;
use Netvlies\Bundle\PublishParametersBundle\Form\MysqlType;
use Netvlies\Bundle\PublishParametersBundle\Entity\MysqlParams;

class MysqlAdmin extends AdminTarget
{

    /**
     * @var MysqlParams $entity
     */
    protected $entity;

    /**
     * This method must return the form that you create for your parameterset
     * @return Form
     */
    public function getForm()
    {
        if(empty($this->target)){
            $this->target = new Target();
        }

        $form = $this->createForm(new MysqlType(), $this->target, array());

        return $form;
    }

    /**
     * This must return all parameter keys, that are eventually used in the command
     * @todo might be a better way to have this set in annotations
     * @return mixed
     */
    public function getKeys()
    {
        return array(
            'mysql_user',
            'mysql_password',
            'mysql_dbname',
            'mysql_host',
            'mysql_port'
        );
    }

    /**
     * This must return all parameters in a key value array. It must use the same keys that are returned with
     * the getKeys method
     *  get from annotations metadata
     * @return mixed
     */
    public function getParameters()
    {
        $params = $this->getKeys();
        $params['mysql_user'] = $this->entity->getUsername();
        $params['mysql_password'] = $this->entity->getPassword();
        $params['mysql_dbname'] = $this->entity->getDbname();
        $params['mysql_host'] = $this->entity->getHostname();
        $params['mysql_port'] = $this->entity->getPort();

        return $params;
    }


}
