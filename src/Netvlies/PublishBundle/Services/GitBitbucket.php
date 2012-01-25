<?php
/**
 * @author: M. de Krijger
 * Creation date: 25-1-12
 */


namespace Netvlies\PublishBundle\Services;


use Netvlies\PublishBundle\Entity\Application;

class GitBitbucket
{

    protected $basePath;
    protected $user;
    protected $password;
    protected $owner;
    protected $lastProcessedReference;
    protected $lastChangesets; // last 15 changesets (or less if other branches are updated)

    /**
     * @var \Netvlies\PublishBundle\Entity\Application $app
     */
    protected $app;


    /**
     * @param $repositoryBasePath
     * @param $user
     * @param $password
     * @param $owner
     */
    public function __construct($repositoryBasePath, $user, $password, $owner){
        $this->basePath = $repositoryBasePath;
        $this->user = $user;
        $this->password = $password;
        $this->owner = $owner;
    }


    /**
     * @throws \Exception
     */
    protected function checkApp(){
        if(is_null($this->app)){
            throw new \Exception('Application needs to be set before any other methods can be used');
        }
    }

    /**
     * @param \Netvlies\PublishBundle\Entity\Application $app
     */
    public function setApplication(Application $app){
        $this->app = $app;
    }

    /**
     * Currently not support
     * @return array
     */
    public function getRemoteBranches(){

        $this->checkApp();

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



	public function getLastChangesets()
    {
        $this->checkApp();

        // Reference is bound to application by selecting it in a form
        // so we can use the internal getter
        $reference = $this->app->getReferenceToDeploy();

        if($this->lastProcessedReference == $reference){
            return $this->lastChangesets;
        }

        // @see documentation about following curl command at http://confluence.atlassian.com/display/BITBUCKET/Changesets
        $ch = curl_init();
        $url = 'https://api.bitbucket.org/1.0/repositories/'.$this->owner.'/'.$this->app->getRepoKey().'/changesets/?start='.$reference.'&limit=15';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->password);
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

        $this->lastChangesets = $branchesets;
        return $this->lastChangesets;
	}

    /**
     * @return string
     */
    public function getBitbucketChangesetURL(){
        $this->checkApp();
        return 'https://bitbucket.org/'.$this->owner.'/'.$this->app->getRepoKey().'/changesets';
    }

    /**
     * @return string
     */
    public function getAbsolutePath(){
        return $this->basePath . '/' . $this->app->getRepoKey();
    }

    /**
     * @return string Absolute path of the build.xml file
     */
    public function getBuildFile(){
        return $this->getAbsolutePath().'/build.xml';
    }
}
