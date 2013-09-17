<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Versioning;

use Netvlies\Bundle\PublishBundle\Entity\Application;

interface VersioningInterface
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
     * Return an array with ReferenceInterface of all deployable branches and tags for given application
     *
     * @abstract
     * @param $app
     * @return array
     */
    function getBranchesAndTags(Application $app);

    /**
     * Returns array with all tags
     *
     * @param Application $app
     * @return mixed
     */
    function getTags(Application $app);


    /**
     * Returns array with all branches
     *
     * @param Application $app
     * @return mixed
     */
    function getBranches(Application $app);

    /**
     * Must return an URL which shows the latest commitlogs
     *
     * @abstract
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @param $branch
     * @return array()
     */
    function getCommitLog(Application $app, $branch);


    /**
     * @param Application $app
     * @return CommitInterface
     */
    function getHeadRevision(Application $app);


    /**
     * This must return the locally checked out repository
     *
     * @param \Netvlies\Bundle\PublishBundle\Entity\Application $app
     * @return mixed
     */
    function getRepositoryPath(Application $app);


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
    function getPrivateKey();
}