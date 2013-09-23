<?php

namespace Netvlies\Bundle\PublishBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Netvlies\Bundle\PublishBundle\Tests\Files\GitRepo;

class TargetControllerTest extends WebTestCase
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

    public function testTargetsView()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadTarget',
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/application/1/targets');

        $this->assertTrue($crawler->filter('html:contains("remote setup")')->count() > 0);
    }

    public function testTargetCreate()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment',
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/application/1/target/new/step1');
        $this->assertTrue($crawler->filter('html:contains("Adding target for")')->count() > 0);

        $form= $crawler->selectButton('Next')->form();

        // @todo for every type DTAP
        $form['netvlies_publishbundle_target_step1[environment]'] = '1';
        $form['netvlies_publishbundle_target_step1[username]'] = 'user';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $form = $crawler->selectButton('Save')->form();
        $form['netvlies_publishbundle_target_step2[label]'] = 'testlabel';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Target testlabel is added")')->count() > 0);
    }


    public function testEdit()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadTarget',
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/target/edit/1');
        $form= $crawler->selectButton('Save')->form();

        $form['netvlies_publishbundle_targetedittype[label]'] = 'testedit';

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Target testedit is updated")')->count() > 0);
    }

    public function testDelete()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment',
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadTarget',
        ));

        $client = static::createClient();
        $client->request('GET', '/target/delete/1');
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Target testtarget is deleted")')->count() > 0);
    }

}
