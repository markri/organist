<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */

namespace Netvlies\Bundle\PublishBundle\Strategy;

class Strategy {

    private $keyname;

    private $label;

//    private $rvm;

    private $defaultCommands;

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
//
//    /**
//     * @return mixed
//     */
//    public function getRvm()
//    {
//        return $this->rvm;
//    }
//
//    /**
//     * @param mixed $rvm
//     */
//    public function setRvm($rvm)
//    {
//        $this->rvm = $rvm;
//    }

    /**
     * @return mixed
     */
    public function getDefaultCommands()
    {
        return $this->defaultCommands;
    }

    /**
     * @param mixed $defaultCommands
     */
    public function createDefaultCommands($defaultCommands)
    {
        $this->defaultCommands = $defaultCommands;
    }

    /**
     * @return mixed
     */
    public function getKeyname()
    {
        return $this->keyname;
    }

    /**
     * @param mixed $keyname
     */
    public function setKeyname($keyname)
    {
        $this->keyname = $keyname;
    }
}
