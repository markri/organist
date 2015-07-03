<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Netvlies\Bundle\PublishBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('netvlies_publish_default_index'));
        $this->assertTrue($crawler->filter('html:contains("Top applications")')->count() > 0);
    }


    public function testNotAuthorized()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('netvlies_publish_default_notauthorized'));
        $this->assertTrue($crawler->filter('html:contains("not authorized")')->count() > 0);
    }

}
