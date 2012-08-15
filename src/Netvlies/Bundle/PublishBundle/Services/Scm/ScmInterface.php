<?php
/**
 * @author: M. de Krijger
 * Creation date: 25-1-12
 */
namespace Netvlies\Bundle\PublishBundle\Services\Scm;

use Netvlies\Bundle\PublishBundle\Entity\Application;

interface ScmInterface
{
    /**
     * Must update your repository
     *
     * @abstract
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    function updateRepository(Application $app);

    /**
     * Must create the repository (usually remote)
     *
     * @abstract
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    function createRepository(Application $app);

    /**
     * Must return last changeset
     *
     * @abstract
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return ChangeSetInterface
     */
    function getLastChangeset(Application $app);


    /**
     * Must checkout/clone a repository
     *
     * @abstract
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return boolean
     */
    function checkoutRepository(Application $app);

    /**
     * Get changeset array for given application
     *
     * @abstract
     * @param $app
     * @param $fromRef
     * @param $toRef
     * @return array
     */
    function getChangesets(Application $app, $fromRef, $toRef);

    /**
     * Return an array with strings of all deployable branches and tags for given application
     *
     * @abstract
     * @param $app
     * @return array
     */
    function getBranchesAndTags(Application $app);

    /**
     * Checks if repository already exists for given application keyname
     *
     * @abstract
     * @param $app
     * @return boolean
     */
    function existRepository(Application $app);

    /**
     * Must return an URL which shows the latest commitlogs
     *
     * @abstract
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return string
     */
    function getViewCommitLogURL(Application $app);

    /**
     * Will return an URL which should show a diff for given commit reference/revision
     * @abstract
     * @param Appliction $app
     * @return string
     */
    function getViewCommitURL(Appliction $app, $reference);

    /**
     * This returns the (usually remote) URL of the repository, which must be used for checking out
     *
     * @abstract
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return string
     */
    function getRepositoryURL(Application $app);
}
