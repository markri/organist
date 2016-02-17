<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Versioning\Git;

use GitElephant\GitBinary;
use GitElephant\Objects\TreeBranch;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use GitElephant\Repository;
use GitElephant\Objects\Log;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\BranchCommand;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\LogCommand;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\ResetCommand;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\FetchCommand;
use Netvlies\Bundle\PublishBundle\Versioning\Git\GitElephant\TagCommand;
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


    /**
     * @param $baseRepositoryPath
     * @param string $privateKey
     */
    public function __construct($baseRepositoryPath, $privateKey = '')
    {
        $this->baseRepositoryPath = $baseRepositoryPath;
        $this->privateKey = $privateKey;
    }

    /**
     * Must update your repository
     * This method is kind of expensive when there are many branches.
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    public function updateRepository(Application $app)
    {
        $repo = $this->getRepository($app);
        // sync tags with origin
        $repo->getCaller()->execute(TagCommand::getInstance()->syncAllTags());

        //sync branches with origin (fetch --all ) this will sync remotes/origin/* but no local branches that tracks one of the branches in here
        $repo->getCaller()->execute(FetchCommand::getInstance()->fetchOrigin());

        // Ensure that every remote branch is locally available, so its deployment descriptors can be used
        $repo->checkoutAllRemoteBranches(); // remotes/origin/mybranch will be mybranch
        $remoteBranches = $this->getRemoteBrancheNames($app);

        // Get all local branches and reset them to origin/{branchname}
        $branches = $repo->getBranches();
        foreach($branches as $branch){
            /**
             * @var TreeBranch $branch
             */
            if(in_array($branch->getName(), $remoteBranches)){
                $repo->checkout($branch->getName());
                $repo->getCaller()->execute(ResetCommand::getInstance()->resetCurrentBranch('origin/'.$branch->getName()));
            }
            else{
                // We dont have to delete the "untracked" branch, $this->getBranches() will only return the remote branches
                // Force removal of local branch in case it is not on remote
                //$repo->getCaller()->execute(BranchCommand::getInstance()->forceDelete($branch->getName()));
            }
        }
    }


    /**
     * Must checkout/clone a repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    public function checkoutRepository(Application $app)
    {
        $repoPath = $this->getRepositoryPath($app);
        exec('mkdir -p '.escapeshellarg($repoPath));

        $repo = $this->getRepository($app);
        try{
            $repo->cloneFrom($app->getScmUrl(), $repoPath);
            $repo->checkoutAllRemoteBranches();
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
    public function getChangesets(Application $app, $fromRef, $toRef)
    {
        $repo = $this->getRepository($app);
        $outputLines = $repo->getCaller()->execute(LogCommand::getInstance()->getCommitMessagesBetween($fromRef, $toRef))->getOutputLines();
        $logs = Log::createFromOutputLines($repo, $outputLines);

        $changeset = array();
        foreach($logs as $log){
            $msg = $log->getAuthor();
            if(empty($msg)){
                // Skip empty logs
                continue;
            }

            $msg = $log->getMessage()->toString();

            $commit = new Commit();
            $commit->setMessage($msg);
            $commit->setReference($log->getSha());
            $commit->setAuthor($log->getAuthor()->getName());
            $commit->setDateTime($log->getDatetimeCommitter());
            $changeset[] = $commit;
        }

        return $changeset;
    }

    /**
     * Return an array with strings of all deployable branches and tags for given application
     *
     * @param $app
     * @return array
     */
    public function getBranchesAndTags(Application $app)
    {
        return array_merge($this->getBranches($app), $this->getTags($app));
    }


    /**
     * Returns array with all branches
     * Only return remote branches. A local untracked branch that is checked out will be skipped by then and remains there for historical purposes
     *
     * @param Application $app
     * @return array
     */
    public function getBranches(Application $app)
    {
        $repo = $this->getRepository($app);
        $repo->checkout('master'); // Switch to master, because when we're in detached state after deployment, output will be useless for git elephant
        $branches = $repo->getBranches();

        $remoteBranches = $this->getRemoteBrancheNames($app);
        $references = array();

        foreach($branches as $branch){
            /**
             * @var \GitElephant\Objects\Branch $branch
             */
            if(!in_array($branch->getName(), $remoteBranches)){
                // Skip untracked branches that do not exist on remote
                continue;
            }

            $reference = new Reference();
            $reference->setReference($branch->getSha());
            $reference->setName($branch->getName());
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
    public function getTags(Application $app)
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
     * @param Application $app
     * @return CommitInterface
     */
    public function getHeadRevision(Application $app)
    {
        $repo = $this->getRepository($app);

        $logCommand = LogCommand::getInstance();
        $outputLines = $repo->getCaller()->execute($logCommand->showAllLog('HEAD', null, 1), true, $repo->getPath())->getOutputLines();
        $log = Log::createFromOutputLines($repo, $outputLines)->first();

        $commit = new Commit();
        $commit->setMessage($log->getMessage());
        $commit->setReference($log->getSha());
        $commit->setAuthor($log->getAuthor()->getName().' <'.$log->getAuthor()->getEmail().'>');
        $commit->setDateTime($log->getDatetimeCommitter());

        return $commit;
    }


    /**
     * This must return the local checked out repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return mixed
     */
    public function getRepositoryPath(Application $app)
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
    public function getPrivateKey()
    {
        return $this->privateKey;
    }


    protected function getRepository(Application $app)
    {
        if(empty($this->repository)){
            $repoPath = $this->getRepositoryPath($app);
            $gitBinary = new GitBinary('/usr/bin/git');
            $this->repository = new Repository($repoPath, $gitBinary);
        }

        return $this->repository;
    }


    /**
     * Internal helper method to just return remote Branchenames (optionally with full path /remotes/origin)
     * @param Application $app
     * @return array with Branchnames
     */
    protected function getRemoteBrancheNames(Application $app, $fullPath=false)
    {
        $allBranches = $this->getRepository($app)->getBranches(true, true);
        $remoteBrancheNames = array_filter($allBranches, function($branch) {
            return preg_match('/^remotes(.+)$/', $branch)
            && !preg_match('/^(.+)(HEAD)(.*?)$/', $branch);
        });

        if(!$fullPath){
            $branches = array();
            foreach($remoteBrancheNames as $name){
                $branches[] = basename($name);
            }
            $remoteBrancheNames = $branches;
        }

        return $remoteBrancheNames;
    }
}
