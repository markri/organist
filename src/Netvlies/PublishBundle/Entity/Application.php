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
     * @ORM\Column(name="gitrepoSSH", type="string", length=255, nullable=true)
     */
    private $gitrepoSSH;

    /**
     * @ORM\Column(name="gitrepokey", type="string", length=255, nullable=true)
     */
    private $gitrepokey;

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
    private $referenceToDeploy;
    private $applicableChangesets = array();


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
     * @param ApplicationType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return ApplicationType
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
     * Set gitrepoSSH
     *
     * @param string $gitrepoSSH
     */
    public function setGitrepoSSH($gitrepoSSH)
    {
        $this->gitrepoSSH = $gitrepoSSH;
    }

    /**
     * Get gitrepoSSH
     *
     * @return string 
     */
    public function getGitrepoSSH()
    {
        return $this->gitrepoSSH;
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
     * Currently not support
     * @return array
     */
    public function getRemoteBranches(){

        if(!file_exists($this->getAbsolutePath())){
            return array();
        }

        $path = $this->getAbsolutePath();
        $command = 'cd '.$path.'; git ls-remote 2>&1';
        $output = shell_exec($command);

        $regex = '/\n(.{40})\s*(.*)$/im';
        $matches = array();
        $numberfound = preg_match_all($regex, $output, $matches);

        if($numberfound == 0){
            return array();
        }

        $refs = $matches[1];
        $branchnames = $matches[2];

        $return = array_combine($refs, $branchnames);
        return $return;
    }


	
	public function processBitbucketReference($bbuser, $bbpassword)
    {
        // Reference is bound to this entity by selecting it in a form
        // so we can use the internal getter
        $reference = $this->getReferenceToDeploy();
        //use bitbucketrepoowner from container


        // @see documentation about following curl command at http://confluence.atlassian.com/display/BITBUCKET/Changesets
        $ch = curl_init();

        //@todo we use this->getName in doing this we assume that the name is exactly the same as the bitbucket key, which could be different
        $url = 'https://api.bitbucket.org/1.0/repositories/netvlies/'.$this->getName().'/changesets/?start='.$reference.'&limit=15';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $bbuser.':'.$bbpassword);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);

        $assoc = json_decode($json, true);
        $changesets = $assoc['changesets'];
        $topdownchangesets = array_reverse($changesets);

        $allowednodes = $topdownchangesets[0]['parents'];
        $branchesets = array();
        $branchesets[] = $topdownchangesets[0];

        foreach($topdownchangesets as $changeset){
            if(in_array($changeset['node'], $allowednodes)){
                $branchesets[] = $changeset;
                $allowednodes = array_merge($allowednodes, $changeset['parents']);
            }
        }

        $this->applicableChangesets = $branchesets;
	}

    public function getBitbucketChangesetURL(){
        //@todo we use this->getName in doing this we assume that the name is exactly the same as the bitbucket key, which could be different
        //@todo also the netvlies key is kind of fixed, which is not reusable
        return 'https://bitbucket.org/netvlies/'.$this->getName().'/changesets';
    }



    public function setMysqlpw($mysqlpw)
    {
        $this->mysqlpw = $mysqlpw;
    }

    public function getMysqlpw()
    {
        return $this->mysqlpw;
    }

    public function setReferenceToDeploy($referenceToDeploy)
    {
        $this->referenceToDeploy = $referenceToDeploy;
    }

    public function getReferenceToDeploy()
    {
        return $this->referenceToDeploy;
    }

    public function setApplicableChangesets($applicableChangesets)
    {
        $this->applicableChangesets = $applicableChangesets;
    }

    public function getApplicableChangesets()
    {
        return $this->applicableChangesets;
    }

    public function setGitrepokey($gitrepokey)
    {
        $this->gitrepokey = $gitrepokey;
    }

    public function getGitrepokey()
    {
        return $this->gitrepokey;
    }

}