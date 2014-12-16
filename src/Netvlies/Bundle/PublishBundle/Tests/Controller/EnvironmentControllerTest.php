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
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;
use Symfony\Component\Process\Process;

class EnvironmentControllerTest extends WebTestCase
{

    /**
     * Also contains redirect to dashboard which is asserted
     */
    public function testCreateEnvironment()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/environment/create');

        $this->assertTrue($crawler->filter('html:contains("Add host")')->count() > 0);

        $form= $crawler->selectButton('Save')->form();

        $form['environment_create[type]'] = 'D';
        $form['environment_create[hostname]'] = 'test_hostname';
        $form['environment_create[port]'] = '22';

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("test_hostname")')->count() > 0);
    }


    public function testEditEnvironment()
    {
        // Load fixture with one app present
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/environment/edit/1');

        $this->assertTrue($crawler->filter('html:contains("either IP or DNS")')->count() > 0 );

        $form= $crawler->selectButton('Save')->form();

        $form['environment_create[hostname]'] = 'newhostname';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("newhostname")')->count() > 0);
    }

    public function testDeleteEnvironment()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadEnvironment'
        ));

        $client = static::createClient();
        $client->request('GET', '/environment/delete/1');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Oops, no environments available")')->count() > 0);
    }

}

