<?php
/**
 * (c) Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netvlies\Bundle\PublishBundle\Versioning\Git;

use GitElephant\Objects\TreeBranch;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use GitElephant\Repository;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\SyncBranchesCommand;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\SyncTagsCommand;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\ResetBranchCommand;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\Reference;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\Commit;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\BranchCommand;
use Netvlies\Bundle\PublishBundle\Versioning\CommitInterface;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;


class Git implements VersioningInterface
{

    /**
     * @var string $baseRepositoryPath
     */
    protected $baseRepositoryPath;

    /**
     * @var string $privateKey
     */
    protected $privateKey;

    /**
     * @var Repository $repository
     */
    protected $repository;

    public function __construct($baseRepositoryPath, $config)
    {
        $privateKey = array_key_exists('forward_key', $config) ? $config['forward_key'] : '';
        $this->baseRepositoryPath = $baseRepositoryPath;
        $this->privateKey = $privateKey;
    }

    protected function getRepository(Application $app)
    {
        if(empty($this->repository)){
            $repoPath = $this->getRepositoryPath($app);
            $this->repository = new Repository($repoPath);
        }

        return $this->repository;
    }

    /**
     * Must update your repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    function updateRepository(Application $app)
    {
        $repo = $this->getRepository($app);
        // sync tags with origin
        $repo->getCaller()->execute(SyncTagsCommand::getInstance()->syncAllTags());

        //sync branches with origin (fetch --all --prune) this will sync remotes/origin/* but no local branches that tracks one of the branches in here
        $repo->getCaller()->execute(SyncBranchesCommand::getInstance()->syncAllBranches());

        // Ensure that every remote branch is locally available, so its deployment descriptors can be used
        $repo->checkoutAllRemoteBranches(); // remotes/origin/mybranch will be mybranch




        $allBranches = $repo->getBranches(true, true);
        $remoteBranches = array_filter($allBranches, function($branch) {
            return preg_match('/^remotes(.+)$/', $branch)
            && !preg_match('/^(.+)(HEAD)(.*?)$/', $branch);
        });



        // Get all tracked branches and reset them to origin/{branchname}
        $branches = $repo->getBranches();
        foreach($branches as $branch){
            /**
             * @var TreeBranch $branch
             */
            $originBranch = 'origin/'.$branch->getName();
            if(in_array('remotes/'.$originBranch, $remoteBranches)){
                $repo->checkout($branch->getName());
                $repo->getCaller()->execute(ResetBranchCommand::getInstance()->resetCurrentBranch($originBranch));
            }
            else{
                // Force removal of local branch in case it is not on remote
                $repo->getCaller()->execute(BranchCommand::getInstance()->forceDelete($branch->getName()));
            }
        }
    }


    /**
     * Must return last changeset
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return ChangeSetInterface
     */
    function getLastChangeset(Application $app)
    {
        // TODO: Implement getLastChangeset() method.
    }

    /**
     * Must checkout/clone a repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    function checkoutRepository(Application $app)
    {
        $repoPath = $this->getRepositoryPath($app);
        exec('mkdir -p '.escapeshellarg($repoPath));

        $repo = $this->getRepository($app);
        try{
            $repo->cloneFrom($app->getScmUrl(), $repoPath);
        }
        catch(\Exception $e){
            exec('rm -rf '.escapeshellarg($repoPath));
            throw $e;
        }
    }

    /**
     * Get changeset array for given application
     *
     * @param $app
     * @param $fromRef
     * @param $toRef
     * @return array
     */
    function getChangesets(Application $app, $fromRef, $toRef)
    {
        // TODO: Implement getChangesets() method.
    }

    /**
     * Return an array with strings of all deployable branches and tags for given application
     *
     * @param $app
     * @return array
     */
    function getBranchesAndTags(Application $app)
    {
        return array_merge($this->getBranches($app), $this->getTags($app));
    }


    /**
     * Returns array with all branches
     *
     * @param Application $app
     * @return array
     */
    function getBranches(Application $app)
    {
        $repo = $this->getRepository($app);
        $repo->checkout('master'); // Switch to master, because when we're in detached state, output will be useless for git elephant

        $branches =$repo->getBranches();
        $references = array();

        foreach($branches as $branche){
            /**
             * @var \GitElephant\Objects\TreeBranch $branche
             */
            $reference = new Reference();
            $reference->setReference($branche->getSha());
            $reference->setName($branche->getName());
            $references[] = $reference;
        }

        return $references;
    }


    /**
     * Returns array with all tags
     *
     * @param Application $app
     * @return array
     */
    function getTags(Application $app)
    {
        $repo = $this->getRepository($app);
        $repo->checkout('master'); // Switch to master, because when we're in detached state, output will be useless for git elephant

        $tags = $repo->getTags();
        $references = array();

        foreach($tags as $tag){
            /**
             * @var \GitElephant\Objects\TreeTag $tag
             */
            $reference = new Reference();
            $reference->setReference($tag->getSha());
            $reference->setName($tag->getName());
            $references[] = $reference;
        }

        return $references;
    }


    /**
     * Must return an URL which shows the latest commitlogs
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return array()
     */
    function getCommitLog(Application $app)
    {
        // TODO: Implement getCommitLog() method.
    }

    /**
     * This must return the local checked out repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return mixed
     */
    function getRepositoryPath(Application $app)
    {
        return $this->baseRepositoryPath. DIRECTORY_SEPARATOR . $app->getKeyName();
    }

    /**
     * This is for optional private keys that should be forwarded to target host when deploying.
     * During deployment.
     *
     * - Organist will forward private key (for github/bitbucket/...) to target host
     * - Organist connects to target  host
     * - From target host a connection will be made to the versioning server to retrieve right version
     *
     * Maybe a deployement should be precompiled by tarballing it and then uploading it to target host. But currently
     * capifony is built in a way that composer is fetched and executed on target machine. So currently it is much
     * more easier to use these predefined functions rather than making it custom.
     *
     * @return string
     */
    function getPrivateKey()
    {
        return $this->privateKey;
    }


}
