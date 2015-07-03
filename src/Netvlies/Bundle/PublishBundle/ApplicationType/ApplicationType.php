<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */
namespace Netvlies\Bundle\PublishBundle\ApplicationType;

class ApplicationType
{

    private $label;

    private $keyname;

    private $userdirs;

    private $userfiles;

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

    /**
     * @return mixed
     */
    public function getUserdirs()
    {
        return $this->userdirs;
    }

    /**
     * @param mixed $userdirs
     */
    public function setUserdirs($userdirs)
    {
        $this->userdirs = $userdirs;
    }

    /**
     * @return mixed
     */
    public function getUserfiles()
    {
        return $this->userfiles;
    }

    /**
     * @param mixed $userfiles
     */
    public function setUserfiles($userfiles)
    {
        $this->userfiles = $userfiles;
    }



}
