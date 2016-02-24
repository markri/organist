<?php

namespace Netvlies\Bundle\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationType
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ApplicationType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var array
     *
     * @ORM\Column(name="userDirs", type="array")
     */
    private $userDirs;

    /**
     * @var array
     *
     * @ORM\Column(name="userFiles", type="array")
     */
    private $userFiles;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ApplicationType
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set userdirs
     *
     * @param array $userdirs
     * @return ApplicationType
     */
    public function setUserdirs($userdirs)
    {
        $this->userdirs = $userdirs;
    
        return $this;
    }

    /**
     * Get userdirs
     *
     * @return array 
     */
    public function getUserdirs()
    {
        return $this->userdirs;
    }

    /**
     * Set userFiles
     *
     * @param array $userFiles
     * @return ApplicationType
     */
    public function setUserFiles($userFiles)
    {
        $this->userFiles = $userFiles;
    
        return $this;
    }

    /**
     * Get userFiles
     *
     * @return array 
     */
    public function getUserFiles()
    {
        return $this->userFiles;
    }
}
