<?php

namespace Netvlies\Bundle\PublishBundle\Versioning\Jenkins;
use Netvlies\Bundle\PublishBundle\Entity\Application;
use Netvlies\Bundle\PublishBundle\Versioning\CommitInterface;
use Netvlies\Bundle\PublishBundle\Versioning\Git\Commit;
use Netvlies\Bundle\PublishBundle\Versioning\Git\Reference;
use Netvlies\Bundle\PublishBundle\Versioning\VersioningInterface;
use GuzzleHttp;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Created by PhpStorm.
 * User: mdekrijger
 * Date: 26-7-16
 * Time: 16:26
 */
class Jenkins implements VersioningInterface
{


    private $baseRepositoryPath;

    private $references;

    public function __construct($baseRepositoryPath)
    {
        //http://jenkins.build.nvsotap.nl/job/Armarium/api/json?pretty=true&depth=2&tree=builds[artifacts[relativePath],displayName,number,timestamp,result]
        //
        $this->baseRepositoryPath = $baseRepositoryPath;
    }

    function updateRepository(Application $app)
    {
        return;
    }

    function checkoutRepository(Application $app)
    {
        $repoPath = $this->getRepositoryPath($app);
        exec('mkdir -p '.escapeshellarg($repoPath));
    }

    function checkoutRevision(Application $app, $revision)
    {

        $builds = $this->fetchJenkins($app);
        $artifact = null;

        foreach ($builds['builds'] as $build) {

            if (empty($build['artifacts'])) {
                // No artifacts present for this build
                continue;
            }

            if ($build['number'] != $revision) {
                continue;
            }

            // Fetch first artifact!
            $artifact = $build['artifacts'][0]['relativePath'];
            break;
        }

        if(empty($artifact)) {
            throw  new \Exception('Couldnt find artifact on Jenkins');
        }

     	$destination = $this->getRepositoryPath($app) . DIRECTORY_SEPARATOR . 'tarbal.tar.gz';

        if (file_exists($destination)) {
            unlink($destination);
        }

        $artifactUrl = $app->getScmUrl() . '/' . $revision . '/artifact/' . $artifact ; // e.g. http://jenkins.build.nvsotap.nl/job/Armarium/41/artifact/build.tar.gz
        $client = new GuzzleHttp\Client();
        $client->request('GET', $artifactUrl, [
	    'sink' => $destination
        ]);
    
        # don't extract tarball for deployment descriptors, we put deployment descriptors right here
        # currently no way to manage these DD, except in own repository, maybe manage these DD in organist self (future release)
        # exec(sprintf('cd %s && tar -zxf tarbal.tar.gz  --strip 1', $this->getRepositoryPath($app)));
    }

    function getChangesets(Application $app, $fromRef, $toRef)
    {
        return;
    }

    function getBranchesAndTags(Application $app)
    {
        $builds = $this->fetchJenkins($app);

        if (!empty($this->references)) {
            return $this->references;
        }

        foreach ($builds['builds'] as $build) {

            if (empty($build['artifacts'])) {
                // No artifacts present for this build
                continue;
            }

            if ($build['result'] != 'SUCCESS') {
                //Only fetch succesfull builds
                continue;
            }

            $reference = new Reference();
            $reference->setReference($build['number']);
            $reference->setName($build['displayName']);
            $this->references[] = $reference;
        }

        return $this->references;
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
        $builds = $this->fetchJenkins($app);

        if (!empty($this->references)) {
            return $this->references;
        }

        // Get first build
        $build = $builds['builds'][0];

        $commit = new Commit();
        $commit->setMessage($build['displayName']);
        $commit->setReference($build['number']);
        $commit->setAuthor('jenkins');
        $commit->setDateTime(new \DateTime('@' . intval($build['timestamp']/1000)));

        return $commit;
    }

    function getRepositoryPath(Application $app)
    {
        return $this->baseRepositoryPath. DIRECTORY_SEPARATOR . $app->getKeyName();

    }

    function getPrivateKey()
    {
        return;
    }


    private function fetchJenkins(Application $app)
    {
        $jenkinsBaseUrl = $app->getScmUrl(); // something like: http://jenkins.build.nvsotap.nl/job/Armarium
        $buildsUrl = $jenkinsBaseUrl . '/api/json?pretty=true&depth=2&tree=builds[artifacts[relativePath],displayName,number,timestamp,result]';

        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $buildsUrl);

        if ($res->getStatusCode() != '200') {
            return array();
        }

        return json_decode($res->getBody(), true);
    }

}
