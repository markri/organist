<?php

namespace Netvlies\Bundle\PublishBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($crawler->filter('html:contains("Application list")')->count() > 0);
    }


    public function testNotAuthorized()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connect-fail');
        $this->assertTrue($crawler->filter('html:contains("not authorized")')->count() > 0);
    }

}
