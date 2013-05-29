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

    public function __construct($appHelper)
    {
        $this->appHelper = $appHelper;
    }

    /**
     * Must update your repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    function updateRepository(Application $app)
    {
        $repoPath = $this->appHelper->getRepositoryPath($app);
        $repository = new Repository($repoPath);
    }

    /**
     * Must create the repository (usually remote)
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    function createRepository(Application $app)
    {
        // TODO: Implement createRepository() method.
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
        // TODO: Implement checkoutRepository() method.
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
        // TODO: Implement existRepository() method.
    }

    /**
     * Must return an URL which shows the latest commitlogs
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return string
     */
    function getViewCommitLogURL(Application $app)
    {
        // TODO: Implement getViewCommitLogURL() method.
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
     * This returns the (usually remote) URL of the repository, which must be used for checking out
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return string
     */
    function getRepositoryURL(Application $app)
    {
        // TODO: Implement getRepositoryURL() method.
    }

}
