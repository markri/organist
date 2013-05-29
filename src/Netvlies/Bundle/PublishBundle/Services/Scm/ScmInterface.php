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
     * Must return an URL which shows the latest commitlogs
     *
     * @abstract
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return array()
     */
    function getCommitLog(Application $app);


    /**
     * This must return the locally checked out repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return mixed
     */
    function getRepositoryPath(Application $app);
}
