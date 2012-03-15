<?php
/**
 * @author: M. de Krijger
 * Creation date: 25-1-12
 */
namespace Netvlies\PublishBundle\Services\Scm;

use Netvlies\PublishBundle\Entity\Application;

interface ScmInterface
{

    function getChangesetURL($app);

    function createRepository($app);

    function getChangesets($app, $fromRef, $toRef);

    function getBranches($app);

    function existRepo($app);

    /**
     * @abstract
     * return strin if set or null when no keyfile is present
     */
    function getKeyfile();

}
