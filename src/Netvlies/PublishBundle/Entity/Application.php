<?php

namespace Netvlies\PublishBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Netvlies\PublishBundle\Entity\ScriptBuilder;

/**
 * Netvlies\PublishBundle\Entity\Application
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Netvlies\PublishBundle\Entity\ApplicationRepository")
 *
 */
class Application
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="name is required")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

   /**
    * @Assert\NotBlank(message="customer is required")
    * @ORM\Column(name="customer", type="string", length=255)
    */
    private $customer;

    /**
     * @var ApplicationType $type
     * @ORM\ManyToOne(targetEntity="ApplicationType")
     */
    private $type;

    /**
     * @Assert\Regex(pattern="/^git@bitbucket.org:.*?.git$/", match=true, message="Use GIT SSH connection string from Bitbucket")
     * @ORM\Column(name="gitrepo", type="string", length=255, nullable=true)
     */
    private $gitrepo;

    /**
     * @ORM\Column(name="mysqlpw", type="string", length=255, nullable=true)
     */
    private $mysqlpw;


    /**
     * @var object $userFiles
     * @ORM\OneToMany(targetEntity="UserFiles", mappedBy="application")
     */
    private $userFiles;


    private $baseRepositoriesPath = '/';

    private $branchToDeploy;

    private $revisionDataloaded = false;
    private $lastRevisionId;
	private $lastRevisionMessage;
	private $lastRevisionAuthor;
	


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
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
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
     * Set gitrepo
     *
     * @param string $gitrepo
     */
    public function setGitrepo($gitrepo)
    {
        $this->gitrepo = $gitrepo;
    }

    /**
     * Get gitrepo
     *
     * @return string 
     */
    public function getGitrepo()
    {
        return $this->gitrepo;
    }

    /**
     * Set userFiles
     *
     * @param object $userFiles
     */
    public function setUserFiles($userFiles)
    {
        $this->userFiles = $userFiles;
    }

    /**
     * Get userFiles
     *
     * @return object
     */
    public function getUserFiles()
    {
        return $this->userFiles;
    }

    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }


    /**
     * This is a basic setter for the base repositories path
     * Based on this parameter, all other directories can be calculated
     *
     * @param $baseReposPath
     */
    public function setBaseRepositoriesPath($baseRepositoriesPath){
        $this->baseRepositoriesPath = $baseRepositoriesPath;
    }


    public function getAbsolutePath(){
        return $this->baseRepositoriesPath . '/' . $this->getName();
    }

    /**
     * @return string Absolute path of the build.xml file
     */
    public function getBuildFile(){
        return $this->getAbsolutePath().'/build.xml';
    }

    /**
     * @todo later on we need to make a distinction based on different roles
     * e.g. a PM will see different targets compared to a developer, maybe we can do this
     */
    public function parseTargets(){
        $sBuildFile = $this->getBuildFile();
		$oXML = simplexml_load_file($sBuildFile);

		$aTargets = array();

		foreach($oXML->target as $oTarget){
			if(isset($oTarget['name']) ){
				$aTargets[] = array('name'=>(string)$oTarget['name'], 'description'=>(string)$oTarget['description']);
			}
		}

		return $aTargets;
    }


    public function getBranches(){

        if(!file_exists($this->getAbsolutePath())){
            return array();
        }

        $repository = new \Git\Repository($this->getAbsolutePath());
        $manager = new \Git\Reference\Manager($repository);
        $list = $manager->getList();
        $branches = array();
        $refsToShow = array('/refs/remotes');

        foreach($list as $key=>$ref){
            foreach($refsToShow as $showRef){
                if(false === strpos($ref->name, $showRef)){
                    continue;
                }

                $branches[$ref->getId()] =$ref->name;
            }
        }

        return $branches;
    }

    public function getGitUpdateScript(){
        $scriptBuilder = new ScriptBuilder(time());
        $scriptBuilder->setWorkingDirectory($this->getAbsolutePath());
        $scriptBuilder->addLine('git pull --all');
        return $scriptBuilder->getEncodedScriptPath();
    }
	
	protected function processLastRevision()
    {

        if($this->revisionDataloaded || empty($this->branchToDeploy)){
            return;
        }

        $repository = new \Git\Repository($this->getAbsolutePath());

        $this->lastRevisionId = $this->branchToDeploy;
        $lastcommit = $repository->getCommit($this->lastRevisionId);

        $this->lastRevisionAuthor = $lastcommit->getCommitter()->name;
        $this->lastRevisionMessage= $lastcommit->getMessage();
        $this->revisionDataloaded = true;
	}

    public function setMysqlpw($mysqlpw)
    {
        $this->mysqlpw = $mysqlpw;
    }

    public function getMysqlpw()
    {
        return $this->mysqlpw;
    }

    public function setBranchToDeploy($branchToDeploy)
    {
        $this->branchToDeploy = $branchToDeploy;
    }

    public function getBranchToDeploy()
    {
        return $this->branchToDeploy;
    }

    public function setLastRevisionId($lastRevisionId)
    {
        $this->lastRevisionId = $lastRevisionId;
    }

    public function getLastRevisionId()
    {
        $this->processLastRevision();
        return $this->lastRevisionId;
    }

    public function getLastRevisionMessage()
    {
        $this->processLastRevision();
        return $this->lastRevisionMessage;
    }

    public function getLastRevisionAuthor()
    {
        $this->processLastRevision();
        return $this->lastRevisionAuthor;
    }


}