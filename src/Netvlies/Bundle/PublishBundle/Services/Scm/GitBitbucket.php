<?php
/**
 * @author: M. de Krijger
 * Creation date: 25-1-12
 */

namespace Netvlies\Bundle\PublishBundle\Services\Scm;

use Netvlies\Bundle\PublishBundle\Entity\Application;

/**
 * @todo implement GitElephantBundle
 *
 *
 *
 */
class GitBitbucket implements ScmInterface
{

    protected $basePath;
    protected $user;
    protected $password;

    public function setContainer($container)
    {
        $this->basePath =      $container->getParameter('netvlies_publish.scm.bitbucket.repositorypath');
        $this->user =          $container->getParameter('netvlies_publish.scm.bitbucket.user');
        $this->password =      $container->getParameter('netvlies_publish.scm.bitbucket.password');

        if(!file_exists($this->basePath)){
            throw new \Exception('Repository base path doesnt exist, please change your config.yml');
        }
    }


    public function updateRepository(Application $app)
    {

    }

    /**
     * Must checkout/clone a repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    function checkoutRepository(Application $app)
    {
        // TODO: Implement checkoutRepository() method.
    }

    /**
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return ChangeSet
     */
    function getLastChangeset(Application $app)
    {
        // TODO: Implement getLastChangeset() method.
    }

    /**
     * Return an array with strings of all deployable branches and tags for given application
     *
     * @param $app
     * @return array
     */
    function getBranchesAndTags(Application $app)
    {
        // TODO: Implement getBranchesAndTags() method.
    }

    /**
     * Checks if repository already exists for given application keyname
     *
     * @param $app
     * @return boolean
     */
    function existRepository(Application $app)
    {
        // @see documentation about following curl command at http://confluence.atlassian.com/display/BITBUCKET/Repositories
        $ch = curl_init();
        $url = 'https://api.bitbucket.org/1.0/repositories/'.$this->user.'/'.$app->getKeyName().'/';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);

        $repoObject = json_decode($json);
        return !is_null($repoObject);
    }

    /**
     * Must return an URL which shows the latest commitlogs
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return string
     */
    function getViewCommitLogURL(Application $app)
    {
        return 'https://bitbucket.org/'.$this->user.'/'.$app->getKeyName().'/changesets';
    }

    /**
     * Will return an URL which should show a diff for given commit reference/revision
     * @param Appliction $app
     * @return string
     */
    function getViewCommitURL(Appliction $app, $reference)
    {
        // TODO: Implement getViewCommitURL() method.
    }

    /**
     * This will be executed locally. So be sure to fetch latest updates from bitbucket otherwise new branches will be missing
     * @return array
     * @todo load from local branches (we should assume up to date local repository)
     */
    public function getBranches($app){


        //git branch -v --abbrev=40



        // Code below is for remote branches
//        $appPath = $app->getAbsolutePath($this->basePath);
//        if(!file_exists($appPath)){
//            throw new \Exception('Repository path doesnt exist '.$appPath);
//        }
//
//        $command = 'cd '.$appPath.'; git ls-remote 2>&1; echo $?;';
//        $output = shell_exec($command);
//        $exitcode = trim(substr($output, -2));
//
//        if($exitcode!='0'){
//            throw new \Exception('Cant read remote branches. Do you have a correct ssh key setup?');
//        }
//
//        $regex = '/\n(.{40})\s*(.*)$/im';
//        $matches = array();
//        $numberfound = preg_match_all($regex, $output, $matches);
//
//        if($numberfound == 0){
//            return array();
//        }
//
//        $refs = $matches[1];
//        $branchnames = $matches[2];
//
//        $return = array_combine($refs, $branchnames);
//
//        return $return;
    }



    /**
     * This will be executed locally
     * @todo refactor in changeset object
     * @todo load from local changes
     * @param $toRef Latest reference to calculate from.
     * @return array|\false|mixed
     */
	public function getChangesets($app, $fromRef, $toRef)
    {

        // @see documentation about following curl command at http://confluence.atlassian.com/display/BITBUCKET/Changesets
        $startRef = empty($toRef)?'':'&start='.$toRef;
        $ch = curl_init();
        $url = 'https://api.bitbucket.org/1.0/repositories/'.$this->owner.'/'.$app->getScmKey().'/changesets/?&limit=15'.$startRef;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);

        $assoc = json_decode($json, true);
        if(is_null($assoc)){
            return array();
        }

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

        return $branchesets;
	}




    /**
     * @return bool
     */
    public function createRepository(Application $app)
    {
        // @see documentation about following curl command at http://confluence.atlassian.com/display/BITBUCKET/Repositories
        $ch = curl_init();
        $url = 'https://api.bitbucket.org/1.0/repositories/';

        $post = array(
            'name' => $app->getScmKey(),
            'scm' => 'git',
            'is_private'=> 'True'
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $result!==false && $status==200;
    }

    /**
     * @param $app
     * @return string
     */
    public function getRepositoryURL(Application $app)
    {
        return 'git@bitbucket.org:'.$this->user.'/'.$app->getKeyName().'.git';
    }

}
