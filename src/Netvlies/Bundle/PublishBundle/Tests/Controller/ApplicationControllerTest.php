<?php

namespace Netvlies\Bundle\PublishBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;
use Symfony\Component\Process\Process;

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
        $this->loadFixtures(array(
            'Netvlies\Bundle\PublishBundle\Tests\Fixtures\LoadApplication'
        ));

        $client = static::createClient();
        $crawler = $client->request('GET', '/application/1/checkoutrepository');

        $this->assertTrue($crawler->filter('html:contains("Redirecting to /console/exec/1")')->count() > 0 );

        /**
         * @var CommandLog $commandLog
         */
        $commandLog = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('NetvliesPublishBundle:CommandLog')->findOneById(1);

        $this->assertTrue(!empty($commandLog));

        $proces = new Process($commandLog->getCommand());
        $proces->run();

// Debug output
//        $proces->run(function ($type, $buffer) {
//            if (Process::ERR === $type) {
//                echo 'ERR > '.$buffer;
//            } else {
//                echo 'OUT > '.$buffer;
//            }
//        });

        $this->assertTrue($proces->isSuccessful());

        $path = $this->getContainer()->getParameter('netvlies_publish.repositorypath').'/testkey';
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
        $client->request('GET', '/application/1/updaterepository');
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
