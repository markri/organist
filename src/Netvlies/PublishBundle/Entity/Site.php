<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Netvlies\PublishBundle\Entity\Site
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Netvlies\PublishBundle\Entity\SiteRepository")
 */
class Site
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $repository
     *
     * @ORM\Column(name="repository", type="string", length=255)
     */
    private $repository;

    /**
     * @var string $buildfile
     *
     * @ORM\Column(name="buildfile", type="string", length=255)
     */
    private $buildfile;


    protected $absolutePath;
    protected $browsePath;


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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set repository
     *
     * @param string $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get repository
     *
     * @return string 
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Set buildfile
     *
     * @param string $buildfile
     */
    public function setBuildfile($buildfile)
    {
        $this->buildfile = $buildfile;
    }

    /**
     * Get buildfile
     *
     * @return string 
     */
    public function getBuildfile()
    {
        return $this->buildfile;
    }
    
    
	/**
	 * Get all targets for this project as a string array
	 * @return array
	 */
	public function parseTargets()
	{
        $sBuildFile = $this->getAbsolutePath().'/'.$this->buildfile;
		$oXML = simplexml_load_file($sBuildFile);

		$aTargets = array();

		foreach($oXML->target as $oTarget){
			if(isset($oTarget['name']) ){
				$aTargets[] = array('name'=>(string)$oTarget['name'], 'description'=>(string)$oTarget['description']);
			}
		}

		return $aTargets;
	}


	/**
	 * Execute phing target
	 * @param string $sTargetName
	 * @param array $aParams Optional params e.g. array('verion'=>'1.2.1')
	 */
//	public function execTarget($sTargetName, $aParams=array())
//	{
//		$sParams = '';
//		foreach($aParams as $sKey=>$sValue){
//			$sParams.='-D'.$sKey.'='.escapeshellarg($sValue).' ';
//		}
//
//		$sExec = "phing $sTargetName $sParams -f $this->sBuildFile 2>&1";
//		execTarget($sExec, $sTargetName, $this->sProjectName);
//	}

    public function setAbsolutePath($absolutePath)
    {
        $this->absolutePath = $absolutePath;
    }

    public function getAbsolutePath()
    {
        return $this->absolutePath;
    }

    public function setBrowsePath($browsePath)
    {
        $this->browsePath = $browsePath;
    }

    public function getBrowsePath()
    {
        return $this->browsePath;
    }


}