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
use Markri\Bundle\OrganistBundle\Tests\Files\GitRepo;

class CommandControllerTest extends WebTestCase
{

    public function setUp()
    {
        $path = $this->getContainer()->getParameter('organist.repositorypath').'/testkey';
        GitRepo::createRepo($path);
    }

    public function tearDown()
    {
        $path = $this->getContainer()->getParameter('organist.repositorypath').'/testkey';
        GitRepo::deleteRepo($path);
    }

    public function testCommandPanel()
    {
        $this->loadFixtures(array(
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadApplication'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_command_commandpanel', array('application' => 1)));

        $this->assertTrue($crawler->filter('html:contains("Commit message")')->count() > 0);
    }


    public function testDeploy()
    {
        $this->loadFixtures(array(
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadApplication',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadTarget'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_command_commandpanel', array('application' => 1)));

        $form= $crawler->selectButton('Deploy')->form();
        $form['markri_organistbundle_applicationdeploy[revision]'] = GitRepo::$lastCommit;
        $form['markri_organistbundle_applicationdeploy[target]'] = '1';

        $crawler = $client->submit($form);

        // Contains
        $this->assertTrue($crawler->filter('html:contains("Redirecting to /console/exec/1")')->count() > 0);
    }


    public function testViewLogs()
    {
        $this->loadFixtures(array(
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadApplication',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadTarget',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadLog'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_command_listlogs', array('application' => 1)));

        $this->assertTrue($crawler->filter('html:contains("All logs for")')->count() > 0);
    }


    public function testViewLog()
    {
        $this->loadFixtures(array(
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadApplication',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadTarget',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadLog'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_command_viewlog', array('commandlog' => 1)));

        $this->assertTrue($crawler->filter('html:contains("Summary")')->count() > 0);
    }

}
