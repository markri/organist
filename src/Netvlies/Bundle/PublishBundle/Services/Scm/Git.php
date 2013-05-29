<?php
/**
 * (c) Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netvlies\Bundle\PublishBundle\Services\Scm;

use Netvlies\Bundle\PublishBundle\Entity\Application;
use GitElephant\Repository;


class Git implements ScmInterface
{

    /**
     * @var \Netvlies\Bundle\PublishBundle\Services\ApplicationHelper $appHelper
     */
    protected $appHelper;

    /**
     * @var Repository $repository
     */
    protected $repository;

    public function __construct($appHelper)
    {
        $this->appHelper = $appHelper;
    }

    protected function getRepository(Application $app)
    {
        if(empty($this->repository)){
            $repoPath = $this->appHelper->getRepositoryPath($app);
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
        $repo = $this->getRepository($app);
        $repo->checkoutAllRemoteBranches();
        return $repo->getBranches(true, true);
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
        return $this->appHelper->getRepositoryPath($app);
    }


}
