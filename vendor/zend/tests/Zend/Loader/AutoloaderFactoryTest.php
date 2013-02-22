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
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Loader;

use ReflectionClass,
    Zend\Loader\AutoloaderFactory;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Loader
 */
class AutoloaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();
    }

    public function tearDown()
    {
        AutoloaderFactory::unregisterAutoloaders();
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        if (is_array($loaders)) {
            foreach ($loaders as $loader) {
                spl_autoload_unregister($loader);
            }
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Restore original include_path
        set_include_path($this->includePath);
    }

    public function testRegisteringValidMapFilePopulatesAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/_files/goodmap.php',
            ),
        ));
        $loader = AutoloaderFactory::getRegisteredAutoloader('Zend\Loader\ClassMapAutoloader');
        $map = $loader->getAutoloadMap();
        $this->assertTrue(is_array($map));
        $this->assertEquals(2, count($map));
    }

    public function testFactoryDoesNotRegisterDuplicateAutoloaders()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'TestNamespace' => __DIR__ . '/TestAsset/TestNamespace',
                ),
            ),
        ));
        $this->assertEquals(1, count(AutoloaderFactory::getRegisteredAutoloaders()));
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'ZendTest\Loader\TestAsset\TestPlugins' => __DIR__ . '/TestAsset/TestPlugins',
                ),
            ),
        ));
        $this->assertEquals(1, count(AutoloaderFactory::getRegisteredAutoloaders()));
        $this->assertTrue(class_exists('TestNamespace\NoDuplicateAutoloadersCase'));
        $this->assertTrue(class_exists('ZendTest\Loader\TestAsset\TestPlugins\Foo'));
    }

    public function testCanUnregisterAutoloaders()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'TestNamespace' => __DIR__ . '/TestAsset/TestNamespace',
                ),
            ),
        ));
        AutoloaderFactory::unregisterAutoloaders();
        $this->assertEquals(0, count(AutoloaderFactory::getRegisteredAutoloaders()));
    }

    public function testCanUnregisterAutoloadersByClassName()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'TestNamespace' => __DIR__ . '/TestAsset/TestNamespace',
                ),
            ),
        ));
        AutoloaderFactory::unregisterAutoloader('Zend\Loader\StandardAutoloader');
        $this->assertEquals(0, count(AutoloaderFactory::getRegisteredAutoloaders()));
    }

    public function testCanGetValidRegisteredAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'TestNamespace' => __DIR__ . '/TestAsset/TestNamespace',
                ),
            ),
        ));
        $autoloader = AutoloaderFactory::getRegisteredAutoloader('Zend\Loader\StandardAutoloader');
        $this->assertInstanceOf('Zend\Loader\StandardAutoloader', $autoloader);
    }

    public function testDefaultAutoloader()
    {
        AutoloaderFactory::factory();
        $autoloader = AutoloaderFactory::getRegisteredAutoloader('Zend\Loader\StandardAutoloader');
        $this->assertInstanceOf('Zend\Loader\StandardAutoloader', $autoloader);
        $this->assertEquals(1, count(AutoloaderFactory::getRegisteredAutoloaders()));
    }

    public function testGetInvalidAutoloaderThrowsException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        $loader = AutoloaderFactory::getRegisteredAutoloader('InvalidAutoloader');
    }

    public function testFactoryWithInvalidArgumentThrowsException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        AutoloaderFactory::factory('InvalidArgument');
    }

    public function testFactoryWithInvalidAutoloaderClassThrowsException()
    {
        $this->setExpectedException('Zend\Loader\Exception\InvalidArgumentException');
        AutoloaderFactory::factory(array('InvalidAutoloader' => array()));
    }

    public function testCannotBeInstantiatedViaConstructor()
    {
        $reflection = new ReflectionClass('Zend\Loader\AutoloaderFactory');
        $constructor = $reflection->getConstructor();
        $this->assertNull($constructor);
    }
}
