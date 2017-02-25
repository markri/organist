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

class TargetControllerTest extends WebTestCase
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

    public function testViewTargets()
    {
        $this->loadFixtures(array(
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadApplication',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadTarget',
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_target_targets', array('application' => 1)));

        $this->assertTrue($crawler->filter('html:contains("testtarget")')->count() > 0);
    }

    public function testCreateTarget()
    {
        $this->loadFixtures(array(
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadApplication',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment',
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_target_createstep1', array('application' => 1)));
        $this->assertTrue($crawler->filter('html:contains("Adding target for")')->count() > 0);

        $form= $crawler->selectButton('Next')->form();

        // @todo for every type in DTAP
        $form['markri_organistbundle_target_step1[environment]'] = '1';
        $form['markri_organistbundle_target_step1[username]'] = 'user';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $form = $crawler->selectButton('Save')->form();
        $form['markri_organistbundle_target_step2[label]'] = 'testlabel';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Target testlabel is added")')->count() > 0);
    }


    public function testEditTarget()
    {
        $this->loadFixtures(array(
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadApplication',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadTarget',
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('markri_organist_target_edit', array('target' => 1)));
        $form = $crawler->selectButton('Save')->form();

        $form['markri_organistbundle_targetedittype[label]'] = 'testedit';

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Target testedit is updated")')->count() > 0);
    }

    public function testDeleteTarget()
    {
        $this->loadFixtures(array(
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadApplication',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadEnvironment',
            'Markri\Bundle\OrganistBundle\Tests\Fixtures\LoadTarget',
        ));

        $client = static::createClient();
        $client->request('GET', $this->getUrl('markri_organist_target_delete', array('target' => 1)));
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Target testtarget is deleted")')->count() > 0);
    }

}
