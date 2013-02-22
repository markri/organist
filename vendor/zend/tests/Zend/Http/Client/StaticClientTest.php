<?php
/**
 * @namespace
 */
namespace ZendTest\Http\Client;

use Zend\Http\ClientStatic as HTTPClient,
    Zend\Http\Client;


/**
 * This are the test for the prototype of Zend\Http\Client
 *
 * @category   Zend
 * @package    Zend\Http\Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend\Http
 * @group      Zend\Http\Client
 */
class StaticClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Uri for test
     *
     * @var string
     */
    protected $baseuri;

    /**
     * Set up the test case
     */
    protected function setUp()
    {
        if (defined('TESTS_ZEND_HTTP_CLIENT_BASEURI')
            && (TESTS_ZEND_HTTP_CLIENT_BASEURI != false)) {

            $this->baseuri = TESTS_ZEND_HTTP_CLIENT_BASEURI;
            if (substr($this->baseuri, -1) != '/') $this->baseuri .= '/';

        } else {
            // Skip tests
            $this->markTestSkipped("Zend_Http_Client dynamic tests are not enabled in TestConfiguration.php");
        }
    }
    
    /**
     * Test simple GET
     */
    public function testHttpSimpleGet()
    {
        $response= HTTPClient::get($this->baseuri . 'testSimpleRequests.php');
        $this->assertTrue($response->isSuccess());
    }
    
    /**
     * Test GET with query string in URI
     */
    public function testHttpGetWithParamsInUri()
    {
        $response= HTTPClient::get($this->baseuri . 'testGetData.php?foo');
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo',$response->getBody());
    }
    
    /**
     * Test GET with query as params
     */
    public function testHttpMultiGetWithParam()
    {
        $response= HTTPClient::get($this->baseuri . 'testGetData.php',array('foo' => 'bar'));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo',$response->getBody());
        $this->assertContains('bar',$response->getBody());
    }
    
    /**
     * Test simple POST
     */
    public function testHttpSimplePost()
    {
        $response= HTTPClient::post($this->baseuri . 'testPostData.php',array('foo' => 'bar'));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo',$response->getBody());
        $this->assertContains('bar',$response->getBody());
    }
    
    /**
     * Test POST with header Content-Type
     */
    public function testHttpPostContentType()
    {
        $response= HTTPClient::post($this->baseuri . 'testPostData.php',
                                    array('foo' => 'bar'),
                                    array('Content-Type' => Client::ENC_URLENCODED));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo',$response->getBody());
        $this->assertContains('bar',$response->getBody());
    }
}
