<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Uri;

use Zend\Uri\Http as HttpUri,
    PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Uri
 * @group      Zend_Uri_Http
 * @group      Zend_Http
 */
class HttpTest extends TestCase
{
    /**
     * Data Providers
     */

    /**
     * Valid HTTP schemes
     *
     * @return array
     */
    static public function validSchemeProvider()
    {
        return array(
            array('http'),
            array('https'),
            array('HTTP'),
            array('Https'),
        );
    }

    /**
     * Invalid HTTP schemes
     *
     * @return array
     */
    static public function invalidSchemeProvider()
    {
        return array(
            array('file'),
            array('mailto'),
            array('g'),
            array('http:')
        );
    }

    static public function portNormalizationTestsProvider()
    {
        return array(
            array('http://www.example.com:80/foo/bar', 'http://www.example.com/foo/bar'),
            array('http://www.example.com:1234/foo/bar', 'http://www.example.com:1234/foo/bar'),
            array('https://www.example.com:443/foo/bar', 'https://www.example.com/foo/bar'),
            array('https://www.example.com:80/foo/bar', 'https://www.example.com:80/foo/bar'),
            array('http://www.example.com:443/foo/bar', 'http://www.example.com:443/foo/bar'),
        );
    }

    /**
     * Tests
     */

    /**
     * Test that specific schemes are valid for this class
     *
     * @param string $scheme
     * @dataProvider validSchemeProvider
     */
    public function testValidScheme($scheme)
    {
        $uri = new HttpUri;
        $uri->setScheme($scheme);
        $this->assertEquals($scheme, $uri->getScheme());
    }

    /**
     * Test that specific schemes are invalid for this class
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testInvalidScheme($scheme)
    {
        $uri = new HttpUri;
        $this->setExpectedException('Zend\Uri\Exception\InvalidUriPartException');
        $uri->setScheme($scheme);
    }

    /**
     * Test that validateScheme returns false for schemes not valid for use
     * with the HTTP class
     *
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testValidateSchemeInvalid($scheme)
    {
        $this->assertFalse(HttpUri::validateScheme($scheme));
    }

    /**
     * Test that normalizing an HTTP URL removes the port depending on scheme
     *
     * @param string $orig
     * @param string $expected
     * @dataProvider portNormalizationTestsProvider
     */
    public function testNormalizationRemovesPort($orig, $expected)
    {
        $uri = new HttpUri($orig);
        $uri->normalize();
        $this->assertEquals($expected, $uri->toString());
    }

    public function testUserIsNullByDefaultWhenNoUserInfoIsProvided()
    {
        $uri = new HttpUri('http://example.com/');
        $uri->normalize();
        $this->assertNull($uri->getUser());
    }

    public function testPasswordIsNullByDefaultWhenNoUserInfoIsProvided()
    {
        $uri = new HttpUri('http://example.com/');
        $uri->normalize();
        $this->assertNull($uri->getPassword());
    }

    public function testCanParseUsernameAndPasswordFromUriNotContainingPort()
    {
        $uri = new HttpUri('http://user:pass@example.com/');
        $uri->normalize();
        $this->assertEquals('user', $uri->getUser());
        $this->assertEquals('pass', $uri->getPassword());
        $this->assertEquals('example.com', $uri->getHost());
    }

    public function testCanParseUsernameAndPasswordFromUriContainingPort()
    {
        $uri = new HttpUri('http://user:pass@example.com:80/');
        $uri->normalize();
        $this->assertEquals('user', $uri->getUser());
        $this->assertEquals('pass', $uri->getPassword());
        $this->assertEquals('example.com', $uri->getHost());
    }

    public function testCanParseUsernameContainingAtMarkFromUri()
    {
        $uri = new HttpUri('http://user@foo.com:pass@example.com/');
        $uri->normalize();
        $this->assertEquals('user@foo.com', $uri->getUser());
        $this->assertEquals('pass', $uri->getPassword());
        $this->assertEquals('example.com', $uri->getHost());
    }

    public function testCanParsePasswordContainingAtMarkFromUri()
    {
        $uri = new HttpUri('http://user:p@ss@example.com/');
        $uri->normalize();
        $this->assertEquals('user', $uri->getUser());
        $this->assertEquals('p@ss', $uri->getPassword());
        $this->assertEquals('example.com', $uri->getHost());
    }

    public function testUserInfoCanOmitPassword()
    {
        $uri = new HttpUri('http://user@example.com@example.com/');
        $uri->normalize();
        $this->assertEquals('user@example.com', $uri->getUser());
        $this->assertNull($uri->getPassword());
        $this->assertEquals('example.com', $uri->getHost());
    }

    public function testGetPortReturnsDefaultPortHttp()
    {
        $uri = new HttpUri('http://www.example.com/');
        $this->assertEquals(80, $uri->getPort());
    }

    public function testGetPortReturnsDefaultPortHttps()
    {
        $uri = new HttpUri('https://www.example.com/');
        $this->assertEquals(443, $uri->getPort());
    }

    public function testGetPortDoesntModifyUrlHttp()
    {
        $origUri = 'http://www.example.com/foo';
        $uri = new HttpUri($origUri);
        $uri->getPort();
        $this->assertEquals($origUri, $uri->toString());
    }

    public function testGetPortDoesntModifyUrlHttps()
    {
        $origUri = 'https://www.example.com/foo';
        $uri = new HttpUri($origUri);
        $uri->getPort();
        $this->assertEquals($origUri, $uri->toString());
    }
}

