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

class ApplicationControllerTest extends WebTestCase
{

    /**
     * Also contains redirect to dashboard which is asserted
     */
    public function testCreateApplication()
    {
        $this->loadFixtures(array());
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('netvlies_publish_application_create'));

        $this->assertTrue($crawler->filter('html:contains("Versioning service")')->count() > 0);

        $form= $crawler->selectButton('Save')->form();

        $form['application_create[name]'] = 'testname';
        $form['application_create[customer]'] = 'testcustomer';
        $form['application_create[keyname]'] = 'testkeyname';
        $form['application_create[applicationType]'] = 'netvlies_publish.type.symfony23';
        $form['application_create[scmService]'] = 'netvlies_publish.versioning.git';
        $form['application_create[scmUrl]'] = 'testUrl';
        $form['application_create[deploymentStrategy]'] = 'netvlies_publish.strategy.capistrano2';

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Hmmmm, no logs yet")')->count() > 0);
    }


    public function testEditApplication()
    {
        // Load fixture with one app present
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('netvlies_publish_application_edit', array('application' => 1)));

        $this->assertTrue($crawler->filter('html:contains("Shared files and directories")')->count() > 0 );

        $form= $crawler->selectButton('Save')->form();

        $form['application_edit[name]'] = 'newtestname';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("newtestname")')->count() > 0);
    }

    public function testDeleteApplication()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication'
        ));

        $client = static::createClient();
        $client->request('GET', $this->getUrl('netvlies_publish_application_delete', array('application' => 1)));

        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Oops! There are no applications present")')->count() > 0);
    }


    public function testCheckoutRepository()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication'
        ));

        // Be sure that directory doesnt exist, so remove it
        $path = $this->getContainer()->getParameter('netvlies_publish.repositorypath').'/testkey';

        $ps = new Process('rm -rf '.escapeshellarg($path));
        $ps->run();
        $this->assertTrue(!file_exists($path));

        // Run test
        $client = static::createClient();
        $client->followRedirects(false);
        $crawler = $client->request('GET', $this->getUrl('netvlies_publish_application_checkoutrepository', array('application' => 1)));;

        $this->assertTrue($crawler->filter('html:contains("Redirecting to /console/exec/1")')->count() > 0 );

        /**
         * @var CommandLog $commandLog
         */
        $commandLog = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('NetvliesPublishBundle:CommandLog')->findOneById(1);

        $this->assertTrue(!empty($commandLog));
        $proces = new Process($commandLog->getCommand());


        $proces->run(function ($type, $buffer) {
           // Debug output
//            if (Process::ERR === $type) {
//                echo 'ERR > '.$buffer;
//            } else {
//                echo 'OUT > '.$buffer;
//            }
        });

        $this->assertTrue($proces->isSuccessful());

        $this->assertTrue(file_exists($path));
    }

    /**
     * @depends Netvlies\Bundle\PublishBundle\Tests\Controller\ApplicationControllerTest::testCheckoutRepository
     * @todo add test for deleted branches and tags (should be removed)
     */
    public function testUpdateRepository()
    {
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication'
        ));

        $client = static::createClient();
        $client->request('GET', $this->getUrl('netvlies_publish_application_updaterepository', array('application' => 1)));

        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('html:contains("Repository for testname is updated")')->count() > 0);
    }


    public static function tearDownAfterClass()
    {
        $kernelClass = self::getKernelClass();
        $kernel = new $kernelClass('test', true);
        $kernel->boot();

        $path = $kernel->getContainer()->getParameter('netvlies_publish.repositorypath').'/testkey';

        $ps = new Process('rm -rf '.escapeshellarg($path));
        $ps->run();
    }

}

