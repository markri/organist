<?php

namespace Netvlies\Bundle\PublishBundle\Versioning\Jenkins;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Versioning\CommitInterface;
use Netvlies\Bundle\PublishBundle\Versioning\Git\Commit;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;
use GuzzleHttp;

/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 26-7-16
 * Time: 16:26
 */
class Jenkins implements VersioningInterface
{


    private $baseRepositoryPath;


    public function __construct($baseRepositoryPath)
    {
        //http://jenkins.build.nvsotap.nl/job/Armarium/api/json?pretty=true&depth=2&tree=builds[artifacts[relativePath],fullDisplayName,number,timestamp]
        //
        $this->baseRepositoryPath = $baseRepositoryPath;
    }

    function updateRepository(Application $app)
    {
        return;
    }

    function checkoutRepository(Application $app)
    {
        mkdir($this->getRepositoryPath($app));
    }

    function checkoutRevision(Application $app, $revision)
    {
        $artifactUrl = $app->getScmUrl() . '/' . $revision . '/artifact'; // e.g. http://jenkins.build.nvsotap.nl/job/Armarium/41/artifact/build.tar.gz
        $original = GuzzleHttp\Stream\create(fopen($artifactUrl, 'r'));
        $local = GuzzleHttp\Stream\create(fopen($this->getRepositoryPath($app) . DIRECTORY_SEPARATOR . 'tarbal.tar.gz', 'w'));
        $local->write($original->getContents());
    }

    function getChangesets(Application $app, $fromRef, $toRef)
    {
        return;
    }

    function getBranchesAndTags(Application $app)
    {

        $jenkinsBaseUrl = $app->getScmUrl(); // something like: http://jenkins.build.nvsotap.nl/job/Armarium
        $buildsUrl = $jenkinsBaseUrl . '/api/json?pretty=true&depth=2&tree=builds[artifacts[relativePath],fullDisplayName,number,timestamp]';

        $client = ew \GuzzleHttp\Client();
        $res = $client->request('GET', $buildsUrl);

        if ($res->getStatusCode() != '200') {
            return array();
        }

        $builds = json_decode($res->getBody(), true);
        $commits = array();

        foreach ($builds['builds'] as $build) {

            if (empty($build['artifacts'])) {
                // No artifacts present for this build
                continue;
            }
            $build['number'];

            $commit = new Commit();
            $commit->setMessage($build['fullDisplayName']);
            $commit->setReference($build['number']);
            $commit->setAuthor('jenkins');
            $commit->setDateTime(new \DateTime($build['timestamp']));
            $commits[] = $commit;
        }

        return $commits;
    }

    function getTags(Application $app)
    {
        return $this->getBranchesAndTags($app);
    }

    function getBranches(Application $app)
    {
        return $this->getBranchesAndTags($app);
    }

    function getHeadRevision(Application $app)
    {
        $commits = $this->getBranchesAndTags($app);
        if (empty($commits)) {
            return;
        }

        $first = array_shift($commits);
        return $first;
    }

    function getRepositoryPath(Application $app)
    {
        return $this->baseRepositoryPath. DIRECTORY_SEPARATOR . $app->getKeyName();

    }

    function getPrivateKey()
    {
        return;
    }


}