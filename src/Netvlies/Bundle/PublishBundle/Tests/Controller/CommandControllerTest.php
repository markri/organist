<?php
namespace Netvlies\Bundle\PublishBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Netvlies\Bundle\PublishBundle\Tests\Files\GitRepo;

class CommandControllerTest extends WebTestCase
{

    public function setUp()
    {
        $path = $this->getContainer()->getParameter('netvlies_publish.repositorypath').'/testkey';
        GitRepo::createRepo($path);
    }

    public function tearDown()
    {
        $path = $this->getContainer()->getParameter('netvlies_publish.repositorypath').'/testkey';
        GitRepo::deleteRepo($path);
    }

    public function testCommandPanel()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/application/1/commands');

        $this->assertTrue($crawler->filter('html:contains("Commit message")')->count() > 0);

    }


    public function testDeploy()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadTarget'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/application/1/commands');

        $form= $crawler->selectButton('Deploy')->form();

        $form['netvlies_publishbundle_applicationdeploy[revision]'] = 'develop';
        $form['netvlies_publishbundle_applicationdeploy[target]'] = '1';

        $crawler = $client->submit($form);

        // Contains
        $this->assertTrue($crawler->filter('html:contains("Redirecting to /console/exec/1")')->count() > 0);
    }


    public function testViewLogs()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadTarget',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadLog'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/console/logs/1');

        $this->assertTrue($crawler->filter('html:contains("All logs for")')->count() > 0);
    }


    public function testViewLog()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadTarget',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadLog'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/console/1/viewlog/1');

        $this->assertTrue($crawler->filter('html:contains("Summary")')->count() > 0);
    }

}