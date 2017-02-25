<?php
/**
 * This file is part of Organist
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author: markri <mdekrijger@netvlies.nl>
 */

namespace Markri\Bundle\OrganistBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Markri\Bundle\OrganistBundle\Entity\CommandLog;
use Symfony\Component\Process\Process;

class EnvironmentControllerTest extends WebTestCase
{

    /**
     * Also contains redirect to dashboard which is asserted
     */
    public function testCreateEnvironment()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_environment_create'));

        $this->assertTrue($crawler->filter('html:contains("Add server")')->count() > 0);

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
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_environment_edit', array('environment' => 1)));

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
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment'
        ));

        $client = static::createClient();
        $client->request('GET', $this->getUrl('markri_organist_environment_delete', array('environment' => 1)));
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Oops, no servers available")')->count() > 0);
    }

}

