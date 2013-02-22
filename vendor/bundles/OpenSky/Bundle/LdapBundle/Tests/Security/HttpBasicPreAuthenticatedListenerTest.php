<?php

namespace OpenSky\Bundle\LdapBundle\Tests\Security;

use OpenSky\Bundle\LdapBundle\Security\HttpBasicPreAuthenticatedListener;
use Symfony\Component\HttpFoundation\Request;

class HttpBasicPreAuthenticatedListenerTest extends \PHPUnit_Framework_TestCase
{
    private $listener;
    private $method;

    protected function setUp()
    {
        $this->listener = new HttpBasicPreAuthenticatedListener(
            $this->getMock('Symfony\Component\Security\Core\SecurityContext', array(), array(), '', false, false),
            $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface'),
            'ldap.provider'
        );

        $this->method = new \ReflectionMethod($this->listener, 'getPreAuthenticatedData');
        $this->method->setAccessible(true);
    }

    /**
     * @dataProvider provideTestGetPreAuthenticatedData
     */
    public function testGetPreAuthenticatedData($serverParams, $expectedData)
    {
        $request = new Request(array(), array(), array(), array(), array(), $serverParams);

        $this->assertEquals($expectedData, $this->method->invoke($this->listener, $request));
    }

    public function provideTestGetPreAuthenticatedData()
    {
        return array(
            array(
                array('PHP_AUTH_USER' => 'username', 'PHP_AUTH_PW' => 'password'),
                array('username', 'password'),
            ),
            array(
                array('PHP_AUTH_USER' => 'username'),
                array('username', ''),
            ),
        );
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testGetPreAuthenticatedDataBadCredentials()
    {
        $this->method->invoke($this->listener, new Request());
    }
}
