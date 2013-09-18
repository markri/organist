<?php

namespace Netvlies\Bundle\PublishBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ApplicationControllerTest extends WebTestCase
{

    /**
     * Also contains redirect to dashboard which is asserted
     */
    public function testCreate()
    {
        $this->loadFixtures(array());
        $client = static::createClient();
        $crawler = $client->request('GET', '/application/create');

        $this->assertTrue($crawler->filter('html:contains("Versioning service")')->count() > 0);

        $form= $crawler->selectButton('Save')->form();

        $form['application_create[name]'] = 'testname';
        $form['application_create[customer]'] = 'testcustomer';
        $form['application_create[keyname]'] = 'testkeyname';
        $form['application_create[applicationType]'] = 'symfony23';
        $form['application_create[scmService]'] = 'git';
        $form['application_create[scmUrl]'] = 'testUrl';

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Hmmmm, no logs yet")')->count() > 0);
    }


    public function testEdit()
    {
        // Load fixture with one app present
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/application/settings/1');

        $this->assertTrue($crawler->filter('html:contains("Shared files and directories")')->count() > 0 );

        $form= $crawler->selectButton('Save')->form();

        $form['application_edit[name]'] = 'newtestname';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("newtestname")')->count() > 0);
    }

    public function testDelete()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/application/settings/1');
        $link = $crawler->selectLink('Delete application')->link();
        $client->click($link);

        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Oops! There are no applications present")')->count() > 0);
    }


    public function testCheckoutRepository()
    {
        $this->markTestIncomplete('Must be implemented by using command instead of controller');
    }

    public function testUpdateRepository()
    {
        $this->markTestIncomplete('Must be dependant of testCheckoutRepository');
    }

}