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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache\Pattern;
use Zend\Cache;

/**
 * Test class
 */
class TestObjectCache
{
    /**
     * A counter how oftern the method "bar" was called
     */
    public static $fooCounter = 0;

    public $property = 'testProperty';

    public function bar()
    {
        ++self::$fooCounter;
        $args = func_get_args();

        echo 'foobar_output('.implode(', ', $args) . ') : ' . self::$fooCounter;
        return 'foobar_return('.implode(', ', $args) . ') : ' . self::$fooCounter;
    }

    public function __invoke()
    {
        return call_user_func_array(array($this, 'bar'), func_get_args());
    }

    public function emptyMethod() {}

}

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class ObjectCacheTest extends CommonPatternTest
{

    /**
     * @var Zend\Cache\Storage\Adapter
     */
    protected $_storage;

    public function setUp()
    {
        $class = __NAMESPACE__ . '\TestObjectCache';
        $this->_storage = new Cache\Storage\Adapter\Memory();
        $this->_options = new Cache\Pattern\PatternOptions(array(
            'object'  => new $class(),
            'storage' => $this->_storage,
        ));
        $this->_pattern = new Cache\Pattern\ObjectCache();
        $this->_pattern->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testCallEnabledCacheOutputByDefault()
    {
        $this->_testCall(
            'bar',
            array('testCallEnabledCacheOutputByDefault', 'arg2')
        );
    }

    public function testCallDisabledCacheOutput()
    {
        $this->_options->setCacheOutput(false);
        $this->_testCall(
            'bar',
            array('testCallDisabledCacheOutput', 'arg2')
        );
    }

    public function testCallInvoke()
    {
        $this->_options->setCacheOutput(false);
        $this->_testCall('__invoke', array('arg1', 'arg2'));
    }

    public function testGenerateKey()
    {
        $this->markTestIncomplete();

        $args = array('arg1', 2, 3.33, null);
        $this->_pattern->call('emptyMethod', $args);
        $this->assertEquals(
            $this->_storage->getLastKey(), // get the last used key by storage
            $this->_pattern->generateKey('emptyMethod', $args)
        );
    }

    public function testGenerateKeyWithPredefinedCallbackAndArgumentKey()
    {
        $args     = array('arg1', 2, 3.33, null);
        $options = array(
            'callback_key' => 'callback',
            'argument_key' => 'arguments',
        );

        $expectedKey = md5($options['callback_key'].$options['argument_key']);

        $this->assertEquals(
            $expectedKey,
            $this->_pattern->generateKey('emptyMethod', $args, $options)
        );
    }

    public function testGenerateKeyWithPredefinedEntityKey()
    {
        $args    = array('arg1', 2, 3.33, null);
        $options = array(
            'entity_key'   => 'object',
            'argument_key' => 'arguments',
        );
        $callbackKey = $options['entity_key'].'::emptymethod';

        $expectedKey = md5($callbackKey.$options['argument_key']);

        $this->assertEquals(
            $expectedKey,
            $this->_pattern->generateKey('emptyMethod', $args, $options)
        );
    }

    public function testCallUnknownMethodException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_pattern->call('notExiststingMethod');
    }

    public function testSetProperty()
    {
        $this->_pattern->property = 'testSetProperty';
        $this->assertEquals('testSetProperty', $this->_options->getObject()->property);
    }

    public function testGetProperty()
    {
        $this->assertEquals($this->_options->getObject()->property, $this->_pattern->property);
    }

    public function testIssetProperty()
    {
        $this->assertTrue(isset($this->_pattern->property));
        $this->assertFalse(isset($this->_pattern->unknownProperty));
    }

    public function testUnsetProperty()
    {
        unset($this->_pattern->property);
        $this->assertFalse(isset($this->_pattern->property));
    }

    protected function _testCall($method, array $args)
    {
        $returnSpec = 'foobar_return(' . implode(', ', $args) . ') : ';
        $outputSpec = 'foobar_output(' . implode(', ', $args) . ') : ';
        $callback   = array($this->_pattern, $method);

        // first call - not cached
        $firstCounter = TestObjectCache::$fooCounter + 1;

        ob_start();
        ob_implicit_flush(false);
        $return = call_user_func_array($callback, $args);
        $data = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($returnSpec . $firstCounter, $return);
        $this->assertEquals($outputSpec . $firstCounter, $data);

        // second call - cached
        ob_start();
        ob_implicit_flush(false);
        $return = call_user_func_array($callback, $args);
        $data = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($returnSpec . $firstCounter, $return);
        if ($this->_options->getCacheOutput()) {
            $this->assertEquals($outputSpec . $firstCounter, $data);
        } else {
            $this->assertEquals('', $data);
        }
    }

}
